<!--
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2020 by Daniel Brendel

    Version: 0.1
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
-->

@extends('layouts.layout_home')

@section('content')
    <div class="columns is-centered is-vcentered">
        <div class="column is-three-fifths">
            <?php echo file_get_contents(public_path() . '/data/tac.txt'); ?>
        </div>
    </div>
@endsection