/*
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2024 by Daniel Brendel

    Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

const HELPREALM_ENDPOINT = 'http://localhost:8000';

window.__helprealm_widget = null;

class HelpRealmWidget {
    element = null;
    config = {};
    showWidget = true;
    openForm = false;

    constructor(cfg = {})
    {
        this.config = cfg;

        this.config.elem = (typeof cfg.elem !== 'undefined') ? cfg.elem : null;
        this.config.workspace = (typeof cfg.workspace !== 'undefined') ? cfg.workspace : null;
        this.config.apiKey = (typeof cfg.apiKey !== 'undefined') ? cfg.apiKey : null;
        this.config.header = (typeof cfg.header !== 'undefined') ? cfg.header : null;
        this.config.logo = (typeof cfg.logo !== 'undefined') ? cfg.logo : null;
        this.config.button = (typeof cfg.button !== 'undefined') ? cfg.button : null;
        this.config.lang = (typeof cfg.lang !== 'undefined') ? cfg.lang : null;
        this.config.ticket = (typeof cfg.ticket !== 'undefined') ? cfg.ticket : null;

        if (this.config.header === null) {
            this.config.header = HELPREALM_ENDPOINT + '/gfx/widget/header.jpg';
        }

        if (this.config.logo === null) {
            this.config.logo = HELPREALM_ENDPOINT + '/gfx/widget/logo.png';
        }

        if (this.config.button === null) {
            this.config.button = HELPREALM_ENDPOINT + '/gfx/widget/button.png';
        }

        this.element = document.querySelector(this.config.elem);
        if (!this.element) {
            throw new Error('Element ' + elem + ' does not exist');
        }

        if (!document.getElementById('helprealm-widget-styles')) {
            let link = document.createElement('link');
            link.id = 'helprealm-widget-styles';
            link.rel = 'stylesheet';
            link.type = 'text/css';
            link.href = HELPREALM_ENDPOINT + '/css/widget.css';
            document.getElementsByTagName('head')[0].appendChild(link);
        }

        if (!document.getElementById('helprealm-widget')) {
            let div = document.createElement('div');
            div.id = 'helprealm-widget';
            div.classList.add('helprealm-widget');
            
            div.innerHTML = `
                <div class="helprealm-widget-header" style="background-image: url('` + this.config.header + `');">
                    <div class="helprealm-widget-header-logo" style="background-image: url('` + this.config.logo + `');"></div>
                </div>

                <div class="helprealm-widget-title">` + this.config.lang.title + `</div>

                <div class="helprealm-widget-form">
                    <div class="helprealm-widget-form-input">
                        <label>` + this.config.lang.lblInputName + `</label>
                        <input type="text" id="helprealm-widget-input-name"/>
                    </div>

                    <div class="helprealm-widget-form-input">
                        <label>` + this.config.lang.lblInputEmail + `</label>
                        <input type="email" id="helprealm-widget-input-email"/>
                    </div>

                    <div class="helprealm-widget-form-input">
                        <label>` + this.config.lang.lblInputSubject + `</label>
                        <input type="text" id="helprealm-widget-input-subject"/>
                    </div>

                    <div class="helprealm-widget-form-input">
                        <label>` + this.config.lang.lblInputMessage + `</label>
                        <textarea id="helprealm-widget-input-message"></textarea>
                    </div>

                    <div class="helprealm-widget-form-input">
                        <label>` + this.config.lang.lblInputFile + `</label>
                        <input type="file" id="helprealm-widget-input-file"/>
                    </div>

                    <div class="helprealm-widget-form-input">
                        <span>
                            <a href="javascript:void(0);" onclick="window.__helprealm_widget.submitForm();">` + this.config.lang.btnSubmit + `</a>
                        </span>

                        <span id="helprealm-widget-form-response"></span>
                    </div>
                </div>
            `;
            
            document.getElementsByTagName('body')[0].appendChild(div);
        }

        this.element.innerHTML = `
            <div class="helprealm-widget-openaction">
                <div class="helprealm-widget-openaction-button" style="background-image: url('` + this.config.button + `');" onclick="window.__helprealm_widget.toggleWidgetForm();"></div>
            </div>
        `;

        window.__helprealm_widget = this;
    }

    widgetVisibilityAction()
    {
        let action = document.querySelector('.helprealm-widget-openaction');
        if (!action) {
            throw new Error('Widget seems not to be placed yet.');
        }

        if (this.showWidget) {
            action.style.display = 'block';
        } else {
            action.style.display = 'none';
        }
    }

    toggleWidgetForm()
    {
        this.openForm = !this.openForm;

        this.formVisibilityAction();
    }

    formVisibilityAction()
    {
        let form = document.querySelector('#helprealm-widget');
        if (!form) {
            throw new Error('Widget seems not to be placed yet.');
        }

        if (this.openForm) {
            form.style.display = 'block';
        } else {
            form.style.display = 'none';
        }
    }

    submitForm()
    {
        let self = this;

        let name = document.querySelector('#helprealm-widget-input-name');
        let email = document.querySelector('#helprealm-widget-input-email');
        let subject = document.querySelector('#helprealm-widget-input-subject');
        let message = document.querySelector('#helprealm-widget-input-message');
        let attachment = document.querySelector('#helprealm-widget-input-file');

        let elResp = document.querySelector('#helprealm-widget-form-response');
        elResp.innerHTML = '&#x27F3;';
        elResp.classList.remove('helprealm-widget-form-response-success');
        elResp.classList.remove('helprealm-widget-form-response-error');

        this.rotDegree = 0;
        this.tmrIndicator = setInterval(function(){
            self.rotDegree++;
            if (self.rotDegree >= 360) {
                self.rotDegree = 0;
            }

            elResp.style.transform = 'rotate(' + self.rotDegree + 'deg)';
        }, 1);

        var data = new FormData();
        data.append('token', this.config.apiKey);
        data.append('name', name.value);
        data.append('email', email.value);
        data.append('subject', subject.value);
        data.append('text', message.value);

        if (attachment.files.length > 0) {
            data.append('attachment', attachment.files[0]);
        }
        
        data.append('type', this.config.ticket.type);
        data.append('prio', this.config.ticket.prio);

        var req = new XMLHttpRequest();
        
        req.onreadystatechange = function() {
            if (req.readyState == XMLHttpRequest.DONE) {
                let json = JSON.parse(req.responseText);

                clearInterval(self.tmrIndicator);
                elResp.style.transform = 'unset';
                
                if (json.code == 201) {
                    elResp.classList.add('helprealm-widget-form-response-success');
                    elResp.innerHTML = '&#x2705;';

                    name.value = '';
                    email.value = '';
                    subject.value = '';
                    message.value = '';
                    attachment.value = '';
                } else if (json.code == 500) {
                    elResp.classList.add('helprealm-widget-form-response-error');
                    elResp.innerHTML = self.config.lang.error.replace('{elem}', json.invalid_fields[0].name);
                } else if (json.code == 403) {
                    elResp.classList.add('helprealm-widget-form-response-error');
                    elResp.innerHTML = self.config.lang.access;
                }
            }
        };

        req.onerror = function() {
            clearInterval(self.tmrIndicator);
            elResp.style.transform = 'unset';
            elResp.classList.add('helprealm-widget-form-response-error');
            elResp.innerHTML = 'Something went wrong!';
        };

        req.open('POST', HELPREALM_ENDPOINT + '/api/' + this.config.workspace + '/widget/ticket/create', true);
        req.send(data);
    }

    showWidget(flag)
    {
        this.showWidget = flag;

        this.widgetVisibilityAction();
    }

    toggleWidget()
    {
        this.showWidget = !this.showWidget;

        this.widgetVisibilityAction();
    }

    isOpened()
    {
        return this.openForm;
    }

    release()
    {
        let styles = document.getElementById('helprealm-widget-styles');
        if (styles) {
            styles.remove();
        }

        let form = document.getElementById('helprealm-widget-form');
        if (form) {
            form.remove();
        }

        let action = document.querySelector('.helprealm-widget-openaction');
        if (action) {
            action.remove();
        }

        if ((typeof window.__helprealm_widget !== 'undefined') && (window.__helprealm_widget !== null)) {
            window.__helprealm_widget = null;
        }
    }
}