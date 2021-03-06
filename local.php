<?php
// -------------------------------------------------------------------- \\
// Если удалить следующие строки файл не будет отображаться в админке
// Но нам не нужна инициализация авторизации т.д и т.п.
// Оставляем в комменте и живём с этим дерьмом дальше
//require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
//$APPLICATION->SetTitle("Форма абитуриента");
// -------------------------------------------------------------------- \\

require __DIR__ . '/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        Handler::make(new Mysql($config), $config['limit'])->handle();
    } catch (\Throwable $ex) {
        http_response_code(418);
    }

    exit(1);
}

$loader = EntrantFormModel::make()
    ->setTitle('Форма абитуриента') // Заголовок страницы
    ->addHeadMetaTag(
        ['http-equiv' => 'Content-Type', 'content' => 'text/html; charset=UTF-8']
    ) // Кодировка странциы в UTF-8
    ->addHeadLinkTag('https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css')
    ->addHeadLinkTag('https://fonts.googleapis.com/icon?family=Material+Icons', ['rel' => 'stylesheet'])
    ->addHeadScriptTag(
        'https://code.jquery.com/jquery-3.6.0.min.js',
        [
            'integrity' => 'sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=',
            'crossorigin' => 'anonymous',
        ]
    ) // Важно что-бы js материалайза подключался после jquery
    ->addHeadScriptTag('https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js');
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <title><?= $loader->getTitle() ?></title>
    <?php
    $loader->loadHeadTags() ?>
    <style>
        .select_valid.invalid {
            border-bottom: 1px solid #F44336;
            -webkit-box-shadow: 0 1px 0 0 #f44336;
            box-shadow: 0 1px 0 0 #f44336;
        }

        .bold {
            font-weight: bold;
        }

        .space {
            height: 4rem;
        }

        /* PrismJS 1.23.0
https://prismjs.com/download.html#themes=prism-okaidia&languages=markup+css+clike+javascript+json+json5 */
        /**
         * okaidia theme for JavaScript, CSS and HTML
         * Loosely based on Monokai textmate theme by http://www.monokai.nl/
         * @author ocodia
         */

        code[class*="language-"],
        pre[class*="language-"] {
            color: #f8f8f2;
            background: none;
            text-shadow: 0 1px rgba(0, 0, 0, 0.3);
            font-family: Consolas, Monaco, 'Andale Mono', 'Ubuntu Mono', monospace;
            font-size: 1em;
            text-align: left;
            white-space: pre;
            word-spacing: normal;
            word-break: normal;
            word-wrap: normal;
            line-height: 1.5;

            -moz-tab-size: 4;
            -o-tab-size: 4;
            tab-size: 4;

            -webkit-hyphens: none;
            -moz-hyphens: none;
            -ms-hyphens: none;
            hyphens: none;
        }

        /* Code blocks */
        pre[class*="language-"] {
            padding: 1em;
            margin: .5em 0;
            overflow: auto;
            border-radius: 0.3em;
        }

        :not(pre) > code[class*="language-"],
        pre[class*="language-"] {
            background: #272822;
        }

        /* Inline code */
        :not(pre) > code[class*="language-"] {
            padding: .1em;
            border-radius: .3em;
            white-space: normal;
        }

        .token.comment,
        .token.prolog,
        .token.doctype,
        .token.cdata {
            color: #8292a2;
        }

        .token.punctuation {
            color: #f8f8f2;
        }

        .token.namespace {
            opacity: .7;
        }

        .token.property,
        .token.tag,
        .token.constant,
        .token.symbol,
        .token.deleted {
            color: #f92672;
        }

        .token.boolean,
        .token.number {
            color: #ae81ff;
        }

        .token.selector,
        .token.attr-name,
        .token.string,
        .token.char,
        .token.builtin,
        .token.inserted {
            color: #a6e22e;
        }

        .token.operator,
        .token.entity,
        .token.url,
        .language-css .token.string,
        .style .token.string,
        .token.variable {
            color: #f8f8f2;
        }

        .token.atrule,
        .token.attr-value,
        .token.function,
        .token.class-name {
            color: #e6db74;
        }

        .token.keyword {
            color: #66d9ef;
        }

        .token.regex,
        .token.important {
            color: #fd971f;
        }

        .token.important,
        .token.bold {
            font-weight: bold;
        }

        .token.italic {
            font-style: italic;
        }

        .token.entity {
            cursor: help;
        }

    </style>
</head>
<body class="container">
<header>
    <div class="row">
        <div class="col l8 offset-l2 m10 offset-m1 s12 center-align">
            <h3>Данные с удостоверения абитуриента.</h3>
            <p>Добро пожаловать в UIB, для того чтобы заполнить данные снизу, вам необходимо воспользоваться
                карт-ридером,
                заполнить все данные через плагин ID2, и нажать кнопку отправить.</p>
        </div>
    </div>
    <div class="divider"></div>
</header>
<div class="section">
    <section class="row">
        <form class="col l8 offset-l2 m10 offset-m1" id="entrant_form">
            <div class="row">
                <div class="input-field col l4 m12 s12">
                    <input name="first_name" id="first_name" type="text" class="validate">
                    <label for="first_name">Имя</label>
                </div>
                <div class="input-field col l4 m12 s12">
                    <input name="last_name" id="last_name" type="text" class="validate">
                    <label for="last_name">Фамилия</label>
                </div>
                <div class="input-field col l4 m12 s12">
                    <input name="second_name" id="second_name" type="text" class="validate">
                    <label for="second_name">Отчество</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col l4 m12 s12 validate select_valid">
                    <select name="gender" id="gender" class="validate" onchange="changeValid()">
                        <option value="" disabled selected>Выбирете пол</option>
                        <option value="male">Мужской</option>
                        <option value="female">Женский</option>
                    </select>
                    <label for="gender">Пол</label>
                </div>
                <div class="input-field col l4 m12 s12">
                    <input name="latin_first_name" id="latin_first_name" type="text" class="validate">
                    <label for="latin_first_name">Имя на латинском</label>
                </div>
                <div class="input-field col l4 m12 s12">
                    <input name="latin_last_name" id="latin_last_name" type="text" class="validate">
                    <label for="latin_last_name">Фамилия на латинском</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col l6 m12 s12">
                    <input name="birth_date" id="birth_date" type="text" class="datepicker validate">
                    <label for="birth_date">Дата рождения</label>
                </div>
                <div class="input-field col l6 m12 s12">
                    <input name="iin" id="iin" type="text" class="validate data-length" data-length="12"
                           onkeyup="this.value = this.value.replace (/[^0-9+]/, '')">
                    <label for="iin">ИИН</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col l12 m12 s12">
                    <input name="issuer" id="issuer" type="text" class="validate">
                    <label for="issuer">Кем выдано</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col l6 m12 s12">
                    <input name="valid_from" id="valid_from" type="text" class="datepicker validate">
                    <label for="valid_from">Дата выдачи</label>
                </div>
                <div class="input-field col l6 m12 s12">
                    <input name="valid_to" id="valid_to" type="text" class="datepicker validate">
                    <label for="valid_to">Дата окончания</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col l6 m12 s12">
                    <input name="doc_num" id="doc_num" type="text" class="validate data-length" data-length="10">
                    <label for="doc_num">Номер удостоверения личности</label>
                </div>
                <div class="input-field col l6 m12 s12">
                    <input name="birth_place" id="birth_place" type="text" class="validate">
                    <label for="birth_place">Место рождения</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col l6 m12 s12">
                    <input name="nation" id="nation" type="text" class="validate">
                    <label for="nation">Национальность</label>
                </div>
                <div class="input-field col l6 m12 s12">
                    <input name="citizen" id="citizen" type="text" class="validate">
                    <label for="citizen">Гражданство</label>
                </div>
            </div>
            <div class="row center-align">
                <div class="col l6 m12">
                    <div class="col l6 m6">
                        <label>
                            <input name="type_group" value="b" type="radio" checked/>
                            <span>Бакалавриат</span>
                        </label>
                    </div>
                    <div class="col l6 m6">
                        <label>
                            <input name="type_group" value="m" type="radio"/>
                            <span>Магистратура</span>
                        </label>
                    </div>
                    <div class="show-on-medium-and-down space"></div>
                </div>
                <div class="col l6 m12 s12">
                    <label>
                        <input type="checkbox" id="personal_data" onchange="checkPersonalData()"/>
                        <span>Согласен на обработку личных данных</span>
                    </label>
                </div>
            </div>
            <div class="row">
                <div class="col l3 m6 s6">
                    <a class="waves-effect waves-light btn disabled" id="submit" onclick="sendLead()">
                        <i class="material-icons right">send</i>Отправить</a>
                </div>
                <div class="col l1 m6 s6">
                    <div class="preloader-wrapper small" id="submit_loader">
                        <div class="spinner-layer spinner-green-only">
                            <div class="circle-clipper left">
                                <div class="circle"></div>
                            </div>
                            <div class="gap-patch">
                                <div class="circle"></div>
                            </div>
                            <div class="circle-clipper right">
                                <div class="circle"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col l8 m12 s12">
                    <div class="card-panel hide" id="response"></div>
                </div>
            </div>
        </form>
    </section>
</div>
<div class="fixed-action-btn">
    <a class="btn-floating btn-large cyan bold">ID² Web</a>
    <ul>
        <li>
            <a class="btn-floating waves-effect waves-light red tooltipped" data-position="bottom"
               data-tooltip="Скопировать настройки" onclick="copyText('#to_copy')">
                <i class="material-icons">content_copy</i>
            </a>
        </li>
        <li>
            <a class="btn-floating cyan waves-effect waves-light darken-1 tooltipped modal-trigger" href="#help_modal"
               data-position="bottom" data-tooltip="Как настроить ID² Web">
                <i class="material-icons">help</i>
            </a>
        </li>
    </ul>
</div>
<!-- Modal Structure -->
<div id="help_modal" class="modal">
    <div class="modal-content">
        <h4>Настройки для ID² Web</h4>
        <p>Скопируйте код ниже и сохраните его в настройках ID² Web</p>
        <!-- Чуток сломаем кодстайл, что-бы он не хромал на выходе -->
        <pre>
<code class="language-JSON5" id="to_copy">{
    "rules": [
        {
            "url": "https://bitrix24.uib.kz/public_links/abiturient/",
            "name": "Platonus v5.1",
            "replacement": "eng2rus",
            "fields": [
                {
                    "selector": "#first_name",
                    "text": "[FNAME]"
                },
                {
                    "selector": "#last_name",
                    "text": "[LNAME]"
                },
                {
                    "selector": "#second_name",
                    "text": "[SNAME]"
                },
                {
                    "selector": "#latin_last_name",
                    "text": "[MRZLN]"
                },
                {
                    "selector": "#latin_first_name",
                    "text": "[MRZFN]"
                },
                {
                    "selector": "#iin",
                    "text": "[IIN]"
                },
                {
                    "selector": "#issuer",
                    "text": "[ISSUER]"
                },
                {
                    "selector": "#doc_num",
                    "text": "[DOCNUM]"
                },
                {
                    "selector": "#birth_date",
                    "text": "[BDATE:dd.mm.yyyy]"
                },
                {
                    "selector": "#valid_from",
                    "text": "[VALID_FROM:dd.mm.yyyy]"
                },
                {
                    "selector": "#valid_to",
                    "text": "[VALID_TO:dd.mm.yyyy]"
                },
                {
                    "selector": "#gender",
                    "select": "[SEX]",
                    "default_value": "male",
                    "selected_values": {
                        "male": "M",
                        "female": "F"
                    }
                },
                {
                    "selector": "#birth_place",
                    "text": "[BPLACE]"
                },
                {
                    "selector": "#nation",
                    "text": "[NATION]"
                },
                {
                    "selector": "#citizen",
                    "text": "[CITIZEN]"
                }
            ]
        }
    ],
    "replacements": {
        "eng2rus": {
            "KAZAKHSTAN": "Казахстан",
            "MINISTRY OF INTERNAL AFFAIRS": "МВД РК",
            "MINISTRY OF JUSTICE": "Министерство юстиции",
            "RUSSIAN FED": "Россия",
            "KAZAKH": "Казах",
            "RUSSIAN": "Русский"
        }
    }
}</code>
</pre>
    </div>
    <div class="modal-footer">
        <a class="waves-effect waves-green btn-flat" onclick="copyText('#to_copy', false)">Скопировать</a>
        <a href="#!" class="modal-close waves-effect waves-green btn-flat">Закрыть</a>
    </div>
</div>
<footer>
    <script type="text/javascript">
        $(document).ready(function () {
            let paramsDatepicker = {
                i18n: {
                    'cancel': 'Отменить',
                    'clear': 'Очистить',
                    'done': 'Принять',
                    'months': [
                        'Январь',
                        'Февраль',
                        'Март',
                        'Апрель',
                        'Май',
                        'Июнь',
                        'Июль',
                        'Август',
                        'Сентябрь',
                        'Октябрь',
                        'Ноябрь',
                        'Декабрь'
                    ],
                    'monthsShort': [
                        'Январь',
                        'Февраль',
                        'Март',
                        'Апрель',
                        'Май',
                        'Июнь',
                        'Июль',
                        'Август',
                        'Сентябрь',
                        'Октябрь',
                        'Ноябрь',
                        'Декабрь'
                    ],
                    'weekdays': [
                        'Воскресенье',
                        'Понедельник',
                        'Вторник',
                        'Среда',
                        'Четверг',
                        'Пятница',
                        'Суббота'
                    ],
                    'weekdaysShort': [
                        'Вс',
                        'Пн',
                        'Вт',
                        'Ср',
                        'Чт',
                        'Пт',
                        'Сб'
                    ],
                    'weekdaysAbbrev': [
                        'Вс',
                        'Пн',
                        'Вт',
                        'Ср',
                        'Чт',
                        'Пт',
                        'Сб'
                    ],
                },
                firstDay: 1,
                format: "dd.mm.yyyy",
                minDate: new Date(1970, 0),
                maxDate: new Date(2038, 0),
                defaultDate: new Date(2000, 0),
            };

            M.AutoInit();
            $('.datepicker').datepicker(paramsDatepicker);
            M.updateTextFields();
            $('input.data-length').characterCounter();
            $('.tooltipped').tooltip();
            $('.modal').modal();

            // Только цифры
            skipByMatch('#iin, #doc_num', /[^0-9]/g);

            // Только дата
            skipByMatch('#birth_date, #valid_from, #valid_to', /[^0-9\.]/g);

            // Только кирилица
            skipByMatch('#first_name, #second_name, #last_name, #issuer, #birth_place, #citizen, #nation', /[^а-яА-ЯӘ-әҰ-ұІ-іҢ-ңҒ-ғҮ-үҚ-қӨ-өҺ-һ\s]/g);

            // Только латиница
            skipByMatch('#latin_first_name, #latin_last_name', /[^a-zA-Z]/g);
        });

        function changeValid() {
            $('.select_valid').removeClass('invalid')
        }

        function copyText(el, body = true) {
            let $tmp = $("<textarea>");
            if (body) {
                $("body").append($tmp);
            } else {
                $(".modal-content").append($tmp);
            }
            $tmp.val($(el).text()).select();
            document.execCommand("copy");
            $tmp.remove();
        }

        function checkPersonalData() {
            let submit = $('#submit'),
                personal_data = $('#personal_data');

            if (personal_data.is(':checked')) {
                submit.removeClass('disabled');
            } else {
                submit.addClass('disabled');
            }
        }

        function skipByMatch(selectors, match) {
            $(selectors).bind("change keyup input click", function () {
                if (this.value.match(match)) {
                    this.value = this.value.replace(match, '');
                }
            });
        }

        function sendLead() {
            let inputs = $('form input, form select, #submit'),
                loader = $('#submit_loader'),
                response = $('#response'),
                form_result = validForm();

            if (!form_result) {
                return;
            }

            inputs.attr('disabled', true);
            loader.addClass('active');

            sendRequest(form_result).done(function (data) {
                response.text(data.message);
                response.addClass('teal accent-3');
            }).fail(function (data) {
                response.addClass('red accent-2');
                response.text(data.responseJSON.message);
            }).always(function (data) {
                loader.removeClass('active');
                response.removeClass('hide');
            });
        }

        function validForm() {
            let form = new FormData(entrant_form),
                result = false,
                has_invalid = false,
                gender = $('#gender');

            if (gender.val() === '' || gender.val() === null) {
                $('.select_valid').addClass('invalid');
                has_invalid = true;
            }

            form.forEach(function (value, key) {
                let item = $('#' + key);
                if (value === '' && key !== 'second_name') {
                    item.addClass('invalid');
                    has_invalid = true;
                }

                if (!isNaN(item.attr('data-length')) && value.length > item.attr('data-length')) {
                    item.addClass('invalid');
                    has_invalid = true;
                }
            });

            if (!has_invalid) {
                result = form;
            }

            return result;
        }

        function sendRequest(form) {
            return $.ajax({
                url: window.location.href,
                dataType: 'json',
                method: 'POST',
                contentType: "application/json; charset=utf-8",
                data: JSON.stringify(prepareData(form)),
            });
        }

        function prepareData(form) {
            let data = {
                'type': $('input[name="type_group"]').val(),
                'form_type': 'local',
            };

            form.forEach(function (value, key) {
                data[key] = value;
            });

            return data;
        }
    </script>
    <script type="text/javascript" async>
        /* PrismJS 1.23.0
https://prismjs.com/download.html#themes=prism-okaidia&languages=markup+css+clike+javascript+json+json5 */
        var _self="undefined"!=typeof window?window:"undefined"!=typeof WorkerGlobalScope&&self instanceof WorkerGlobalScope?self:{},Prism=function(u){var c=/\blang(?:uage)?-([\w-]+)\b/i,n=0,e={},M={manual:u.Prism&&u.Prism.manual,disableWorkerMessageHandler:u.Prism&&u.Prism.disableWorkerMessageHandler,util:{encode:function e(n){return n instanceof W?new W(n.type,e(n.content),n.alias):Array.isArray(n)?n.map(e):n.replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/\u00a0/g," ")},type:function(e){return Object.prototype.toString.call(e).slice(8,-1)},objId:function(e){return e.__id||Object.defineProperty(e,"__id",{value:++n}),e.__id},clone:function t(e,r){var a,n;switch(r=r||{},M.util.type(e)){case"Object":if(n=M.util.objId(e),r[n])return r[n];for(var i in a={},r[n]=a,e)e.hasOwnProperty(i)&&(a[i]=t(e[i],r));return a;case"Array":return n=M.util.objId(e),r[n]?r[n]:(a=[],r[n]=a,e.forEach(function(e,n){a[n]=t(e,r)}),a);default:return e}},getLanguage:function(e){for(;e&&!c.test(e.className);)e=e.parentElement;return e?(e.className.match(c)||[,"none"])[1].toLowerCase():"none"},currentScript:function(){if("undefined"==typeof document)return null;if("currentScript"in document)return document.currentScript;try{throw new Error}catch(e){var n=(/at [^(\r\n]*\((.*):.+:.+\)$/i.exec(e.stack)||[])[1];if(n){var t=document.getElementsByTagName("script");for(var r in t)if(t[r].src==n)return t[r]}return null}},isActive:function(e,n,t){for(var r="no-"+n;e;){var a=e.classList;if(a.contains(n))return!0;if(a.contains(r))return!1;e=e.parentElement}return!!t}},languages:{plain:e,plaintext:e,text:e,txt:e,extend:function(e,n){var t=M.util.clone(M.languages[e]);for(var r in n)t[r]=n[r];return t},insertBefore:function(t,e,n,r){var a=(r=r||M.languages)[t],i={};for(var l in a)if(a.hasOwnProperty(l)){if(l==e)for(var o in n)n.hasOwnProperty(o)&&(i[o]=n[o]);n.hasOwnProperty(l)||(i[l]=a[l])}var s=r[t];return r[t]=i,M.languages.DFS(M.languages,function(e,n){n===s&&e!=t&&(this[e]=i)}),i},DFS:function e(n,t,r,a){a=a||{};var i=M.util.objId;for(var l in n)if(n.hasOwnProperty(l)){t.call(n,l,n[l],r||l);var o=n[l],s=M.util.type(o);"Object"!==s||a[i(o)]?"Array"!==s||a[i(o)]||(a[i(o)]=!0,e(o,t,l,a)):(a[i(o)]=!0,e(o,t,null,a))}}},plugins:{},highlightAll:function(e,n){M.highlightAllUnder(document,e,n)},highlightAllUnder:function(e,n,t){var r={callback:t,container:e,selector:'code[class*="language-"], [class*="language-"] code, code[class*="lang-"], [class*="lang-"] code'};M.hooks.run("before-highlightall",r),r.elements=Array.prototype.slice.apply(r.container.querySelectorAll(r.selector)),M.hooks.run("before-all-elements-highlight",r);for(var a,i=0;a=r.elements[i++];)M.highlightElement(a,!0===n,r.callback)},highlightElement:function(e,n,t){var r=M.util.getLanguage(e),a=M.languages[r];e.className=e.className.replace(c,"").replace(/\s+/g," ")+" language-"+r;var i=e.parentElement;i&&"pre"===i.nodeName.toLowerCase()&&(i.className=i.className.replace(c,"").replace(/\s+/g," ")+" language-"+r);var l={element:e,language:r,grammar:a,code:e.textContent};function o(e){l.highlightedCode=e,M.hooks.run("before-insert",l),l.element.innerHTML=l.highlightedCode,M.hooks.run("after-highlight",l),M.hooks.run("complete",l),t&&t.call(l.element)}if(M.hooks.run("before-sanity-check",l),(i=l.element.parentElement)&&"pre"===i.nodeName.toLowerCase()&&!i.hasAttribute("tabindex")&&i.setAttribute("tabindex","0"),!l.code)return M.hooks.run("complete",l),void(t&&t.call(l.element));if(M.hooks.run("before-highlight",l),l.grammar)if(n&&u.Worker){var s=new Worker(M.filename);s.onmessage=function(e){o(e.data)},s.postMessage(JSON.stringify({language:l.language,code:l.code,immediateClose:!0}))}else o(M.highlight(l.code,l.grammar,l.language));else o(M.util.encode(l.code))},highlight:function(e,n,t){var r={code:e,grammar:n,language:t};return M.hooks.run("before-tokenize",r),r.tokens=M.tokenize(r.code,r.grammar),M.hooks.run("after-tokenize",r),W.stringify(M.util.encode(r.tokens),r.language)},tokenize:function(e,n){var t=n.rest;if(t){for(var r in t)n[r]=t[r];delete n.rest}var a=new i;return I(a,a.head,e),function e(n,t,r,a,i,l){for(var o in r)if(r.hasOwnProperty(o)&&r[o]){var s=r[o];s=Array.isArray(s)?s:[s];for(var u=0;u<s.length;++u){if(l&&l.cause==o+","+u)return;var c=s[u],g=c.inside,f=!!c.lookbehind,h=!!c.greedy,d=c.alias;if(h&&!c.pattern.global){var p=c.pattern.toString().match(/[imsuy]*$/)[0];c.pattern=RegExp(c.pattern.source,p+"g")}for(var v=c.pattern||c,m=a.next,y=i;m!==t.tail&&!(l&&y>=l.reach);y+=m.value.length,m=m.next){var b=m.value;if(t.length>n.length)return;if(!(b instanceof W)){var k,x=1;if(h){if(!(k=z(v,y,n,f)))break;var w=k.index,A=k.index+k[0].length,P=y;for(P+=m.value.length;P<=w;)m=m.next,P+=m.value.length;if(P-=m.value.length,y=P,m.value instanceof W)continue;for(var E=m;E!==t.tail&&(P<A||"string"==typeof E.value);E=E.next)x++,P+=E.value.length;x--,b=n.slice(y,P),k.index-=y}else if(!(k=z(v,0,b,f)))continue;var w=k.index,S=k[0],O=b.slice(0,w),L=b.slice(w+S.length),N=y+b.length;l&&N>l.reach&&(l.reach=N);var j=m.prev;O&&(j=I(t,j,O),y+=O.length),q(t,j,x);var C=new W(o,g?M.tokenize(S,g):S,d,S);if(m=I(t,j,C),L&&I(t,m,L),1<x){var _={cause:o+","+u,reach:N};e(n,t,r,m.prev,y,_),l&&_.reach>l.reach&&(l.reach=_.reach)}}}}}}(e,a,n,a.head,0),function(e){var n=[],t=e.head.next;for(;t!==e.tail;)n.push(t.value),t=t.next;return n}(a)},hooks:{all:{},add:function(e,n){var t=M.hooks.all;t[e]=t[e]||[],t[e].push(n)},run:function(e,n){var t=M.hooks.all[e];if(t&&t.length)for(var r,a=0;r=t[a++];)r(n)}},Token:W};function W(e,n,t,r){this.type=e,this.content=n,this.alias=t,this.length=0|(r||"").length}function z(e,n,t,r){e.lastIndex=n;var a=e.exec(t);if(a&&r&&a[1]){var i=a[1].length;a.index+=i,a[0]=a[0].slice(i)}return a}function i(){var e={value:null,prev:null,next:null},n={value:null,prev:e,next:null};e.next=n,this.head=e,this.tail=n,this.length=0}function I(e,n,t){var r=n.next,a={value:t,prev:n,next:r};return n.next=a,r.prev=a,e.length++,a}function q(e,n,t){for(var r=n.next,a=0;a<t&&r!==e.tail;a++)r=r.next;(n.next=r).prev=n,e.length-=a}if(u.Prism=M,W.stringify=function n(e,t){if("string"==typeof e)return e;if(Array.isArray(e)){var r="";return e.forEach(function(e){r+=n(e,t)}),r}var a={type:e.type,content:n(e.content,t),tag:"span",classes:["token",e.type],attributes:{},language:t},i=e.alias;i&&(Array.isArray(i)?Array.prototype.push.apply(a.classes,i):a.classes.push(i)),M.hooks.run("wrap",a);var l="";for(var o in a.attributes)l+=" "+o+'="'+(a.attributes[o]||"").replace(/"/g,"&quot;")+'"';return"<"+a.tag+' class="'+a.classes.join(" ")+'"'+l+">"+a.content+"</"+a.tag+">"},!u.document)return u.addEventListener&&(M.disableWorkerMessageHandler||u.addEventListener("message",function(e){var n=JSON.parse(e.data),t=n.language,r=n.code,a=n.immediateClose;u.postMessage(M.highlight(r,M.languages[t],t)),a&&u.close()},!1)),M;var t=M.util.currentScript();function r(){M.manual||M.highlightAll()}if(t&&(M.filename=t.src,t.hasAttribute("data-manual")&&(M.manual=!0)),!M.manual){var a=document.readyState;"loading"===a||"interactive"===a&&t&&t.defer?document.addEventListener("DOMContentLoaded",r):window.requestAnimationFrame?window.requestAnimationFrame(r):window.setTimeout(r,16)}return M}(_self);"undefined"!=typeof module&&module.exports&&(module.exports=Prism),"undefined"!=typeof global&&(global.Prism=Prism);
        Prism.languages.markup={comment:/<!--[\s\S]*?-->/,prolog:/<\?[\s\S]+?\?>/,doctype:{pattern:/<!DOCTYPE(?:[^>"'[\]]|"[^"]*"|'[^']*')+(?:\[(?:[^<"'\]]|"[^"]*"|'[^']*'|<(?!!--)|<!--(?:[^-]|-(?!->))*-->)*\]\s*)?>/i,greedy:!0,inside:{"internal-subset":{pattern:/(\[)[\s\S]+(?=\]>$)/,lookbehind:!0,greedy:!0,inside:null},string:{pattern:/"[^"]*"|'[^']*'/,greedy:!0},punctuation:/^<!|>$|[[\]]/,"doctype-tag":/^DOCTYPE/,name:/[^\s<>'"]+/}},cdata:/<!\[CDATA\[[\s\S]*?]]>/i,tag:{pattern:/<\/?(?!\d)[^\s>\/=$<%]+(?:\s(?:\s*[^\s>\/=]+(?:\s*=\s*(?:"[^"]*"|'[^']*'|[^\s'">=]+(?=[\s>]))|(?=[\s/>])))+)?\s*\/?>/,greedy:!0,inside:{tag:{pattern:/^<\/?[^\s>\/]+/,inside:{punctuation:/^<\/?/,namespace:/^[^\s>\/:]+:/}},"special-attr":[],"attr-value":{pattern:/=\s*(?:"[^"]*"|'[^']*'|[^\s'">=]+)/,inside:{punctuation:[{pattern:/^=/,alias:"attr-equals"},/"|'/]}},punctuation:/\/?>/,"attr-name":{pattern:/[^\s>\/]+/,inside:{namespace:/^[^\s>\/:]+:/}}}},entity:[{pattern:/&[\da-z]{1,8};/i,alias:"named-entity"},/&#x?[\da-f]{1,8};/i]},Prism.languages.markup.tag.inside["attr-value"].inside.entity=Prism.languages.markup.entity,Prism.languages.markup.doctype.inside["internal-subset"].inside=Prism.languages.markup,Prism.hooks.add("wrap",function(a){"entity"===a.type&&(a.attributes.title=a.content.replace(/&amp;/,"&"))}),Object.defineProperty(Prism.languages.markup.tag,"addInlined",{value:function(a,e){var s={};s["language-"+e]={pattern:/(^<!\[CDATA\[)[\s\S]+?(?=\]\]>$)/i,lookbehind:!0,inside:Prism.languages[e]},s.cdata=/^<!\[CDATA\[|\]\]>$/i;var t={"included-cdata":{pattern:/<!\[CDATA\[[\s\S]*?\]\]>/i,inside:s}};t["language-"+e]={pattern:/[\s\S]+/,inside:Prism.languages[e]};var n={};n[a]={pattern:RegExp("(<__[^>]*>)(?:<!\\[CDATA\\[(?:[^\\]]|\\](?!\\]>))*\\]\\]>|(?!<!\\[CDATA\\[)[^])*?(?=</__>)".replace(/__/g,function(){return a}),"i"),lookbehind:!0,greedy:!0,inside:t},Prism.languages.insertBefore("markup","cdata",n)}}),Object.defineProperty(Prism.languages.markup.tag,"addAttribute",{value:function(a,e){Prism.languages.markup.tag.inside["special-attr"].push({pattern:RegExp("(^|[\"'\\s])(?:"+a+")\\s*=\\s*(?:\"[^\"]*\"|'[^']*'|[^\\s'\">=]+(?=[\\s>]))","i"),lookbehind:!0,inside:{"attr-name":/^[^\s=]+/,"attr-value":{pattern:/=[\s\S]+/,inside:{value:{pattern:/(=\s*(["']|(?!["'])))\S[\s\S]*(?=\2$)/,lookbehind:!0,alias:[e,"language-"+e],inside:Prism.languages[e]},punctuation:[{pattern:/^=/,alias:"attr-equals"},/"|'/]}}}})}}),Prism.languages.html=Prism.languages.markup,Prism.languages.mathml=Prism.languages.markup,Prism.languages.svg=Prism.languages.markup,Prism.languages.xml=Prism.languages.extend("markup",{}),Prism.languages.ssml=Prism.languages.xml,Prism.languages.atom=Prism.languages.xml,Prism.languages.rss=Prism.languages.xml;
        !function(s){var e=/("|')(?:\\(?:\r\n|[\s\S])|(?!\1)[^\\\r\n])*\1/;s.languages.css={comment:/\/\*[\s\S]*?\*\//,atrule:{pattern:/@[\w-](?:[^;{\s]|\s+(?![\s{]))*(?:;|(?=\s*\{))/,inside:{rule:/^@[\w-]+/,"selector-function-argument":{pattern:/(\bselector\s*\(\s*(?![\s)]))(?:[^()\s]|\s+(?![\s)])|\((?:[^()]|\([^()]*\))*\))+(?=\s*\))/,lookbehind:!0,alias:"selector"},keyword:{pattern:/(^|[^\w-])(?:and|not|only|or)(?![\w-])/,lookbehind:!0}}},url:{pattern:RegExp("\\burl\\((?:"+e.source+"|(?:[^\\\\\r\n()\"']|\\\\[^])*)\\)","i"),greedy:!0,inside:{function:/^url/i,punctuation:/^\(|\)$/,string:{pattern:RegExp("^"+e.source+"$"),alias:"url"}}},selector:RegExp("[^{}\\s](?:[^{};\"'\\s]|\\s+(?![\\s{])|"+e.source+")*(?=\\s*\\{)"),string:{pattern:e,greedy:!0},property:/(?!\s)[-_a-z\xA0-\uFFFF](?:(?!\s)[-\w\xA0-\uFFFF])*(?=\s*:)/i,important:/!important\b/i,function:/[-a-z0-9]+(?=\()/i,punctuation:/[(){};:,]/},s.languages.css.atrule.inside.rest=s.languages.css;var t=s.languages.markup;t&&(t.tag.addInlined("style","css"),t.tag.addAttribute("style","css"))}(Prism);
        Prism.languages.clike={comment:[{pattern:/(^|[^\\])\/\*[\s\S]*?(?:\*\/|$)/,lookbehind:!0,greedy:!0},{pattern:/(^|[^\\:])\/\/.*/,lookbehind:!0,greedy:!0}],string:{pattern:/(["'])(?:\\(?:\r\n|[\s\S])|(?!\1)[^\\\r\n])*\1/,greedy:!0},"class-name":{pattern:/(\b(?:class|interface|extends|implements|trait|instanceof|new)\s+|\bcatch\s+\()[\w.\\]+/i,lookbehind:!0,inside:{punctuation:/[.\\]/}},keyword:/\b(?:if|else|while|do|for|return|in|instanceof|function|new|try|throw|catch|finally|null|break|continue)\b/,boolean:/\b(?:true|false)\b/,function:/\w+(?=\()/,number:/\b0x[\da-f]+\b|(?:\b\d+(?:\.\d*)?|\B\.\d+)(?:e[+-]?\d+)?/i,operator:/[<>]=?|[!=]=?=?|--?|\+\+?|&&?|\|\|?|[?*/~^%]/,punctuation:/[{}[\];(),.:]/};
        Prism.languages.javascript=Prism.languages.extend("clike",{"class-name":[Prism.languages.clike["class-name"],{pattern:/(^|[^$\w\xA0-\uFFFF])(?!\s)[_$A-Z\xA0-\uFFFF](?:(?!\s)[$\w\xA0-\uFFFF])*(?=\.(?:prototype|constructor))/,lookbehind:!0}],keyword:[{pattern:/((?:^|})\s*)catch\b/,lookbehind:!0},{pattern:/(^|[^.]|\.\.\.\s*)\b(?:as|async(?=\s*(?:function\b|\(|[$\w\xA0-\uFFFF]|$))|await|break|case|class|const|continue|debugger|default|delete|do|else|enum|export|extends|finally(?=\s*(?:\{|$))|for|from(?=\s*(?:['"]|$))|function|(?:get|set)(?=\s*(?:[#\[$\w\xA0-\uFFFF]|$))|if|implements|import|in|instanceof|interface|let|new|null|of|package|private|protected|public|return|static|super|switch|this|throw|try|typeof|undefined|var|void|while|with|yield)\b/,lookbehind:!0}],function:/#?(?!\s)[_$a-zA-Z\xA0-\uFFFF](?:(?!\s)[$\w\xA0-\uFFFF])*(?=\s*(?:\.\s*(?:apply|bind|call)\s*)?\()/,number:/\b(?:(?:0[xX](?:[\dA-Fa-f](?:_[\dA-Fa-f])?)+|0[bB](?:[01](?:_[01])?)+|0[oO](?:[0-7](?:_[0-7])?)+)n?|(?:\d(?:_\d)?)+n|NaN|Infinity)\b|(?:\b(?:\d(?:_\d)?)+\.?(?:\d(?:_\d)?)*|\B\.(?:\d(?:_\d)?)+)(?:[Ee][+-]?(?:\d(?:_\d)?)+)?/,operator:/--|\+\+|\*\*=?|=>|&&=?|\|\|=?|[!=]==|<<=?|>>>?=?|[-+*/%&|^!=<>]=?|\.{3}|\?\?=?|\?\.?|[~:]/}),Prism.languages.javascript["class-name"][0].pattern=/(\b(?:class|interface|extends|implements|instanceof|new)\s+)[\w.\\]+/,Prism.languages.insertBefore("javascript","keyword",{regex:{pattern:/((?:^|[^$\w\xA0-\uFFFF."'\])\s]|\b(?:return|yield))\s*)\/(?:\[(?:[^\]\\\r\n]|\\.)*]|\\.|[^/\\\[\r\n])+\/[gimyus]{0,6}(?=(?:\s|\/\*(?:[^*]|\*(?!\/))*\*\/)*(?:$|[\r\n,.;:})\]]|\/\/))/,lookbehind:!0,greedy:!0,inside:{"regex-source":{pattern:/^(\/)[\s\S]+(?=\/[a-z]*$)/,lookbehind:!0,alias:"language-regex",inside:Prism.languages.regex},"regex-flags":/[a-z]+$/,"regex-delimiter":/^\/|\/$/}},"function-variable":{pattern:/#?(?!\s)[_$a-zA-Z\xA0-\uFFFF](?:(?!\s)[$\w\xA0-\uFFFF])*(?=\s*[=:]\s*(?:async\s*)?(?:\bfunction\b|(?:\((?:[^()]|\([^()]*\))*\)|(?!\s)[_$a-zA-Z\xA0-\uFFFF](?:(?!\s)[$\w\xA0-\uFFFF])*)\s*=>))/,alias:"function"},parameter:[{pattern:/(function(?:\s+(?!\s)[_$a-zA-Z\xA0-\uFFFF](?:(?!\s)[$\w\xA0-\uFFFF])*)?\s*\(\s*)(?!\s)(?:[^()\s]|\s+(?![\s)])|\([^()]*\))+(?=\s*\))/,lookbehind:!0,inside:Prism.languages.javascript},{pattern:/(?!\s)[_$a-zA-Z\xA0-\uFFFF](?:(?!\s)[$\w\xA0-\uFFFF])*(?=\s*=>)/i,inside:Prism.languages.javascript},{pattern:/(\(\s*)(?!\s)(?:[^()\s]|\s+(?![\s)])|\([^()]*\))+(?=\s*\)\s*=>)/,lookbehind:!0,inside:Prism.languages.javascript},{pattern:/((?:\b|\s|^)(?!(?:as|async|await|break|case|catch|class|const|continue|debugger|default|delete|do|else|enum|export|extends|finally|for|from|function|get|if|implements|import|in|instanceof|interface|let|new|null|of|package|private|protected|public|return|set|static|super|switch|this|throw|try|typeof|undefined|var|void|while|with|yield)(?![$\w\xA0-\uFFFF]))(?:(?!\s)[_$a-zA-Z\xA0-\uFFFF](?:(?!\s)[$\w\xA0-\uFFFF])*\s*)\(\s*|\]\s*\(\s*)(?!\s)(?:[^()\s]|\s+(?![\s)])|\([^()]*\))+(?=\s*\)\s*\{)/,lookbehind:!0,inside:Prism.languages.javascript}],constant:/\b[A-Z](?:[A-Z_]|\dx?)*\b/}),Prism.languages.insertBefore("javascript","string",{hashbang:{pattern:/^#!.*/,greedy:!0,alias:"comment"},"template-string":{pattern:/`(?:\\[\s\S]|\${(?:[^{}]|{(?:[^{}]|{[^}]*})*})+}|(?!\${)[^\\`])*`/,greedy:!0,inside:{"template-punctuation":{pattern:/^`|`$/,alias:"string"},interpolation:{pattern:/((?:^|[^\\])(?:\\{2})*)\${(?:[^{}]|{(?:[^{}]|{[^}]*})*})+}/,lookbehind:!0,inside:{"interpolation-punctuation":{pattern:/^\${|}$/,alias:"punctuation"},rest:Prism.languages.javascript}},string:/[\s\S]+/}}}),Prism.languages.markup&&(Prism.languages.markup.tag.addInlined("script","javascript"),Prism.languages.markup.tag.addAttribute("on(?:abort|blur|change|click|composition(?:end|start|update)|dblclick|error|focus(?:in|out)?|key(?:down|up)|load|mouse(?:down|enter|leave|move|out|over|up)|reset|resize|scroll|select|slotchange|submit|unload|wheel)","javascript")),Prism.languages.js=Prism.languages.javascript;
        Prism.languages.json={property:{pattern:/(^|[^\\])"(?:\\.|[^\\"\r\n])*"(?=\s*:)/,lookbehind:!0,greedy:!0},string:{pattern:/(^|[^\\])"(?:\\.|[^\\"\r\n])*"(?!\s*:)/,lookbehind:!0,greedy:!0},comment:{pattern:/\/\/.*|\/\*[\s\S]*?(?:\*\/|$)/,greedy:!0},number:/-?\b\d+(?:\.\d+)?(?:e[+-]?\d+)?\b/i,punctuation:/[{}[\],]/,operator:/:/,boolean:/\b(?:true|false)\b/,null:{pattern:/\bnull\b/,alias:"keyword"}},Prism.languages.webmanifest=Prism.languages.json;
        !function(n){var e=/("|')(?:\\(?:\r\n?|\n|.)|(?!\1)[^\\\r\n])*\1/;n.languages.json5=n.languages.extend("json",{property:[{pattern:RegExp(e.source+"(?=\\s*:)"),greedy:!0},{pattern:/(?!\s)[_$a-zA-Z\xA0-\uFFFF](?:(?!\s)[$\w\xA0-\uFFFF])*(?=\s*:)/,alias:"unquoted"}],string:{pattern:e,greedy:!0},number:/[+-]?\b(?:NaN|Infinity|0x[a-fA-F\d]+)\b|[+-]?(?:\b\d+(?:\.\d*)?|\B\.\d+)(?:[eE][+-]?\d+\b)?/})}(Prism);
    </script>
</footer>
</body>
</html>
