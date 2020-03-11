@if ($errors->any())
        <div id="error-message-1">
            <article class="message is-danger">
            <div class="message-header">
                <p>{{ __('error') }}</p>
                <button class="delete" aria-label="delete" onclick="document.getElementById('error-message-1').style.display = 'none';"></button>
            </div>
            <div class="message-body">
                @foreach ($errors->all() as $error)
                    {{ $error }}<br/>
                @endforeach
            </div>
        </article>
        </div>
        <br/>
    @endif

    @if (Session::has('error'))
        <div id="error-message-2">
            <article class="message is-danger">
            <div class="message-header">
                <p>{{ __('error') }}</p>
                <button class="delete" aria-label="delete" onclick="document.getElementById('error-message-2').style.display = 'none';"></button>
            </div>
            <div class="message-body">
                {{ Session::get('error') }}
            </div>
        </article>
        </div>
        <br/>
    @endif

    @if (Session::has('success'))
        <div id="success-message">
            <article class="message is-success">
            <div class="message-header">
                <p>{{ __('success') }}</p>
                <button class="delete" aria-label="delete" onclick="document.getElementById('success-message').style.display = 'none';"></button>
            </div>
            <div class="message-body">
                {{ Session::get('success') }}
            </div>
        </article>
        </div>
        <br/>
    @endif