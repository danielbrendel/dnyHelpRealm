<?php

/*
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2023 by Daniel Brendel

     Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Config;
use PDO;

/**
 * Class InstallerController
 *
 * Perform installer specific computations
 */
class InstallerController extends Controller
{
    /**
     * Return installer view
     *
     * @return mixed
     */
    public function viewInstall()
    {
        if (!file_exists(base_path() . '/do_install')) {
            return __('app.install_product_already_installed');
        }

        $img = 'bg' . random_int(1, 4) . '.jpg';

        $langs = array();
        $dirs = scandir(base_path() . '/resources/lang');
        foreach ($dirs as $dir) {
            if ($dir[0] != '.') {
                array_push($langs, $dir);
            }
        }

        return view('install', ['bgimage' => $img, 'langs' => $langs, 'workspace' => '']);
    }

    /**
     * Perform installation
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function install()
    {
        $attr = request()->validate([
            'database' => 'required',
            'dbuser' => 'required',
            'dbpw' => 'nullable',
            'dbhost' => 'required',
            'dbport' => 'required',
            'lang' => 'required'
        ]);

        if ($attr['dbpw'] == null) $attr['dbpw'] = '';

        if (!file_exists(base_path() . '/do_install')) {
            return back()->with('error', __('app.install_product_already_installed'));
        }

        $envcontent = '# dnyHelpRealm environment configuration file' . PHP_EOL;
        $envcontent .= 'APP_NAME=HelpRealm' . PHP_EOL;
        $envcontent .= 'APP_CODENAME=dnyHelpRealm' . PHP_EOL;
        $envcontent .= 'APP_AUTHOR="Daniel Brendel"' . PHP_EOL;
        $envcontent .= 'APP_VERSION=1.0' . PHP_EOL;
        $envcontent .= 'APP_ENV=production' . PHP_EOL;
        $envcontent .= 'APP_KEY=' . PHP_EOL;
        $envcontent .= 'APP_DEBUG=false' . PHP_EOL;
        $envcontent .= 'APP_URL=' . url('/') . PHP_EOL;
        $envcontent .= 'APP_DESCRIPTION="The lightweight support ticket system"' . PHP_EOL;
        $envcontent .= 'APP_METADESC="Sign up now and get access to a personal customer support workspace"' . PHP_EOL;
		$envcontent .= 'APP_METATAGS="dnyHelpRealm, HelpRealm, daniel brendel, ticket, ticket system, support ticket system, support, system, agent, client, helpdesk"' . PHP_EOL;
        $envcontent .= 'APP_LANG=' . $attr['lang'] . PHP_EOL;
        $envcontent .= 'APP_DESCRIPTION="The free SaaS support ticket system"' . PHP_EOL;
        $envcontent .= 'APP_ALLOWEDHTMLTAGS="<b><i><u><strong><small><ol><ul><li><div><span><a><p><br><code><blockquote><hr>"' . PHP_EOL;
        $envcontent .= 'APP_SUPPORT=null' . PHP_EOL;
        $envcontent .= 'APP_SHOWSTATISTICS=true' . PHP_EOL;
		$envcontent .= 'APP_CAPTCHAFORCUSTOMERREPLIES=false' . PHP_EOL;
		$envcontent .= 'APP_TICKET_CREATION_WAITTIME=10' . PHP_EOL;
		$envcontent .= 'APP_ATTACHMENTS_MAX=10' . PHP_EOL;
        $envcontent .= 'LOG_CHANNEL=stack' . PHP_EOL;
        $envcontent .= 'DB_CONNECTION=mysql' . PHP_EOL;
        $envcontent .= 'DB_HOST="' . $attr['dbhost'] . '"' . PHP_EOL;
        $envcontent .= 'DB_PORT=' . $attr['dbport'] . PHP_EOL;
        $envcontent .= 'DB_DATABASE="' . $attr['database'] . '"' . PHP_EOL;
        $envcontent .= 'DB_USERNAME="' . $attr['dbuser'] . '"' . PHP_EOL;
        $envcontent .= 'DB_PASSWORD="' . $attr['dbpw'] . '"' . PHP_EOL;
		$envcontent .= 'MAILSERV_CRONPW="password"' . PHP_EOL;
		$envcontent .= 'MAILSERV_HOST=localhost' . PHP_EOL;
		$envcontent .= 'MAILSERV_PORT=143' . PHP_EOL;
		$envcontent .= 'MAILSERV_PROTOCOL=imap' . PHP_EOL;
		$envcontent .= 'MAILSERV_ENCRYPTION=tls' . PHP_EOL;
		$envcontent .= 'MAILSERV_VALIDATECERT=true' . PHP_EOL;
		$envcontent .= 'MAILSERV_USERNAME=mailserv' . PHP_EOL;
		$envcontent .= 'MAILSERV_PASSWORD=password' . PHP_EOL;
		$envcontent .= 'MAILSERV_INBOXNAME=INBOX' . PHP_EOL;
		$envcontent .= 'MAILSERV_EMAILADDR="example@domain.tld"' . PHP_EOL;
		$envcontent .= 'STRIPE_TOKEN_SECRET=' . PHP_EOL;
		$envcontent .= 'STRIPE_TOKEN_PUBLIC=' . PHP_EOL;
		$envcontent .= 'STRIPE_COSTS_VALUE=1000' . PHP_EOL;
		$envcontent .= 'STRIPE_COSTS_LABEL="10.00$"' . PHP_EOL;
		$envcontent .= 'STRIPE_CURRENCY="usd"' . PHP_EOL;
		$envcontent .= 'TWITTER_LINK=null' . PHP_EOL;
		$envcontent .= 'TWITTER_IDENT=null' . PHP_EOL;
		$envcontent .= 'GA_TOKEN=null' . PHP_EOL;
        $envcontent .= 'BROADCAST_DRIVER=log' . PHP_EOL;
        $envcontent .= 'CACHE_DRIVER=file' . PHP_EOL;
        $envcontent .= 'QUEUE_CONNECTION=sync' . PHP_EOL;
        $envcontent .= 'SESSION_DRIVER=cookie' . PHP_EOL;
        $envcontent .= 'SESSION_LIFETIME=120' . PHP_EOL;

        if (file_put_contents(base_path() . '/.env', $envcontent) == false) {
            return back()->with('error', __('app.install_env_creation_failure'));
        }

        \Artisan::call('config:clear');
		\Artisan::call('key:generate');

        try {
            $dbobj = new PDO('mysql:host=' . $attr['dbhost'], $attr['dbuser'], $attr['dbpw']);
            $result = $dbobj->exec('CREATE DATABASE IF NOT EXISTS `' . $attr['database'] . '`;');
        } catch (PDOException $e) {
            return back()->with('error', $e->getMessage());
        }

        Config::set('database.connections.mysql', [
            'host' => $attr['dbhost'],
            'port' => $attr['dbport'],
            'database' => $attr['database'],
            'username' => $attr['dbuser'],
            'password' => $attr['dbpw'],
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                \PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ]);

        \DB::reconnect();

        \Artisan::call('migrate:install');
        \Artisan::call('migrate:refresh', array('--path' => 'database/migrations', '--force' => true));

        unlink(base_path() . '/do_install');

        return redirect('/')->with('success', __('app.install_success'));
    }
}
