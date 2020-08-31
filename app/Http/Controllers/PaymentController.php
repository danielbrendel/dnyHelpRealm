<?php

/*
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2020 by Daniel Brendel

     Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

namespace App\Http\Controllers;

use App\AgentModel;
use App\User;
use App\WorkSpaceModel;
use Illuminate\Http\Request;

/**
 * Class PaymentController
 *
 * Handle payment
 */
class PaymentController extends Controller
{
    /**
     * PaymentController constructor.
     *
     * @return void
     */
    public function __construct()
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_TOKEN'));
    }

    /**
     * Perform the payment operation
     *
     * @param $workspace
     * @return \Illuminate\Http\RedirectResponse
     */
    public function charge($workspace)
    {
        try {
            if (!WorkSpaceModel::isLoggedIn($workspace)) {
                return back()->with('error',  __('app.login_required'));
            }

            $ws = WorkSpaceModel::where('name', '=', $workspace)->where('deactivated', '=', false)->first();
            if ($ws === null) {
                return back()->with('error',  __('app.workspace_not_found_or_deactivated'));
            }

            if (!AgentModel::isSuperAdmin(User::getAgent(auth()->id())->id)) {
                return back()->with('error',  __('app.superadmin_permission_required'));
            }

            $attr = request()->validate([
               'stripeToken' => 'required'
            ]);

            $charge = \Stripe\Charge::create([
                'amount' => env('STRIPE_COSTS_VALUE'),
                'currency' => 'usd',
                'description' => $workspace . '/' . User::getAgent(auth()->id())->email,
                'source' => $attr['stripeToken']
            ]);

            if ((!$charge instanceof \Stripe\Charge) || (!isset($charge->status) || ($charge->status !== 'succeeded'))) {
                return back()->with('error', __('app.payment_failed'));
            }

            $ws->paidforapi = true;
            $ws->save();

            return back()->with('success', __('app.payment_succeeded'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
