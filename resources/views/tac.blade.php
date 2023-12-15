{{--
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2023 by Daniel Brendel

     Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
--}}

@extends('layouts.layout_home')

@section('title', __('app.home_tac'))

@section('content')
    <div class="columns is-centered is-vcentered">
        <div class="column is-three-fifths">
            <div class="home-padding">
                <?php echo file_get_contents(public_path() . '/data/tac.html'); ?>
            </div>
        </div>
    </div>
@endsection