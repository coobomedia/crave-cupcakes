(()=>{var o=o=>{let e=o.styleEl;const t=o.cssSelector,n=o.cssRules;"string"==typeof e&&(e=document.querySelector(e)),e&&(e.innerHTML=t+" {"+n+"}")};var e=o=>{let e=o.styleEl;const t=o.styles;if(!Array.isArray(t))return;if("string"==typeof e&&(e=document.querySelector(e)),!e)return;let n="";t.forEach((function(o){n+=o.cssSelector+" {"+o.cssRules+"}"})),e.innerHTML=n};var t=e=>{let t=e.styleEl;const n=e.cssSelector,i=e.keyPrefix?e.keyPrefix:"",r=e.bgPosition;let l=e.bgCustomPosition?e.bgCustomPosition:"";"string"==typeof t&&(t=document.querySelector(t)),t&&(l=l||wp.customize("udb_login["+i+"bg_custom_position]").get(),o({styleEl:t,cssSelector:n,cssRules:"background-position: "+("custom"===r?l:r)+";"}))};var n=e=>{let t=e.styleEl;const n=e.cssSelector,i=e.keyPrefix?e.keyPrefix:"",r=e.bgSize;let l=e.bgCustomSize?e.bgCustomSize:"";"string"==typeof t&&(t=document.querySelector(t)),t&&(l=l||wp.customize("udb_login["+i+"bg_custom_size]").get(),o({styleEl:t,cssSelector:n,cssRules:"background-size: "+("custom"===r?l:r)+";"}))};!function(){wp.customize.bind("preview-ready",(function(){i()}));const i=()=>{r()},r=()=>{wp.customize("udb_login[form_position]",(function(i){const r=document.querySelector('[data-listen-value="udb_login[form_position]"]'),l=document.querySelector('[data-listen-value="udb_login[form_bg_color]"]'),u=document.querySelector('[data-listen-value="udb_login[form_bg_image]"]'),d=document.querySelector('[data-listen-value="udb_login[form_bg_repeat]"]'),s=document.querySelector('[data-listen-value="udb_login[form_bg_position]"]'),c=document.querySelector('[data-listen-value="udb_login[form_bg_size]"]'),g=document.querySelector('[data-listen-value="udb_login[form_width]"]'),m=document.querySelector('[data-listen-value="udb_login[form_horizontal_padding]"]'),a=document.querySelector('[data-listen-value="udb_login[form_border_width]"]');i.bind((function(i){let f=wp.customize("udb_login[form_bg_color]").get();const _=wp.customize("udb_login[form_bg_image]").get(),b=wp.customize("udb_login[form_bg_repeat]").get(),p=wp.customize("udb_login[form_bg_position]").get(),y=wp.customize("udb_login[form_bg_size]").get();let w=wp.customize("udb_login[box_width]").get(),S=wp.customize("udb_login[form_width]").get(),h=wp.customize("udb_login[form_horizontal_padding]").get();f=f||"#ffffff",w=w||"40%",S=S||"320px",h=h||"24px","default"!==i&&("left"===i?r.innerHTML="#login {margin-right: auto; margin-left: 0; min-width: 320px; width: "+w+"; min-height: 100%;} #loginform {max-width: "+S+"; box-shadow: none;}":"right"===i&&(r.innerHTML="#login {margin-right: 0; margin-left: auto; min-width: 320px; width: "+w+"; min-height: 100%;} #loginform {max-width: "+S+"; box-shadow: none;}"),f&&e({styleEl:l,styles:[{cssSelector:"#login",cssRules:"background-color: "+f+";"},{cssSelector:".login form, #loginform",cssRules:"background-color: transparent;"}]}),_&&e({styleEl:u,styles:[{cssSelector:"#login",cssRules:"background-image: url("+_+");"},{cssSelector:".login form, #loginform",cssRules:"background-image: none;"}]}),b&&o({styleEl:d,cssSelector:"#login",cssRules:"background-repeat: "+b+";"}),p&&t({styleEl:s,keyPrefix:"form_",cssSelector:"#login",bgPosition:p}),y&&n({styleEl:c,keyPrefix:"form_",cssSelector:"#login",bgSize:y}),g.innerHTML=g.innerHTML.replace("#login {width:","#loginform {max-width:"),m.innerHTML="#loginform {padding-left: 24px; padding-right: 24px;}",a.innerHTML="#loginform {border-width: 0;}")}))})),wp.customize("udb_login[form_bg_color]",(function(o){const t=document.querySelector('[data-listen-value="udb_login[form_bg_color]"]');o.bind((function(o){var n=wp.customize("udb_login[form_position]").get();o=o||"#ffffff","default"!==(n=n||"default")&&e({styleEl:t,styles:[{cssSelector:"#login",cssRules:"background-color: "+o+";"},{cssSelector:".login form, #loginform",cssRules:"background-color: transparent;"}]})}))})),wp.customize("udb_login[box_width]",(function(o){o.bind((function(o){let e=wp.customize("udb_login[form_position]").get(),t="";e=e||"default","default"!==e&&(t="#login {width: "+o+";}"),document.querySelector('[data-listen-value="udb_login[box_width]"]').innerHTML=t}))})),wp.customize("udb_login[form_width]",(function(o){o.bind((function(o){let e=wp.customize("udb_login[form_position]").get(),t="";e=e||"default",t="default"===e?"#login {width: "+o+";}":"#loginform {max-width: "+o+";}",document.querySelector('[data-listen-value="udb_login[form_width]"]').innerHTML=t}))})),wp.customize("udb_login[form_top_padding]",(function(o){o.bind((function(o){const e="#loginform {padding-top: "+o+";}";document.querySelector('[data-listen-value="udb_login[form_top_padding]"]').innerHTML=e}))})),wp.customize("udb_login[form_bottom_padding]",(function(o){o.bind((function(o){const e="#loginform {padding-bottom: "+o+";}";document.querySelector('[data-listen-value="udb_login[form_bottom_padding]"]').innerHTML=e}))})),wp.customize("udb_login[form_horizontal_padding]",(function(o){o.bind((function(o){const e=o?"#loginform {padding-left: "+o+"; padding-right: "+o+";}":"";document.querySelector('[data-listen-value="udb_login[form_horizontal_padding]"]').innerHTML=e}))})),wp.customize("udb_login[form_border_width]",(function(o){o.bind((function(o){const e=o?"#loginform {border-width: "+o+";}":"";document.querySelector('[data-listen-value="udb_login[form_border_width]"]').innerHTML=e}))})),wp.customize("udb_login[form_border_color]",(function(o){o.bind((function(o){o=o||"#dddddd",document.querySelector('[data-listen-value="udb_login[form_border_color]"]').innerHTML="#loginform {border-color: "+o+";}"}))})),wp.customize("udb_login[form_border_radius]",(function(o){o.bind((function(o){const e=o?"#loginform {border-radius: "+o+";}":"";document.querySelector('[data-listen-value="udb_login[form_border_radius]"]').innerHTML=e}))}))}}()})();
//# sourceMappingURL=preview.js.map
