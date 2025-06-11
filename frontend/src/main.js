
import Vue from 'vue';
import App from './App.vue';
import axios from 'axios';
import jQuery from 'jquery/dist/jquery.js';
import bootstrap from 'bootstrap/dist/js/bootstrap.bundle.min.js';
import BootstrapVue from 'bootstrap-vue';
import moment from 'moment';
import funciones from './otros/Funciones.js';
import vSelect from 'vue-select'

import '@fortawesome/fontawesome-free/css/all.min.css'
//import './otros/fontawesome/css/all.min.css';
import 'jquery-ui-dist/jquery-ui'
import 'jquery-ui-dist/jquery-ui.min.css'
import './otros/sb-admin.css';
import './otros/animate.css';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap-vue/dist/bootstrap-vue.css';
import 'vue-select/dist/vue-select.css';

Vue.use(BootstrapVue);
Vue.use(funciones);

window.$          = window.jQuery = jQuery;
window.axios      = axios;
window.bootstrap  = bootstrap;
//window.Chart      = require("./otros/Chart.js");
//window.Popper     = require("./otros/popper.min.js");
//window.md5        = require("locutus/php/strings/md5.js");
//window.sha1       = require("locutus/php/strings/sha1.js");
//window.Webcam     = require('./otros/webcamjs/webcam.min.js');
window.Vue        = require('vue');

require("./otros/sb-admin.min.js");

//window.base_url = 'https://dev-bodega-central.inverluz.cl/index.php/';
window.base_url = 'index.php/';
document.title = 'Bodega Central';      // CAMBIAR AL NOMBRE QUE CORRESPONDA LA PAGINA

//Vue.config.productionTip = false

/* DEFINICION DE COMPONENTES */

/* LOADER DE CARGA */
Vue.component('loader-component'                , require('./components/otros/Loader.vue'           ).default);

/* VERIFICACION BIOMETRICA */
Vue.component('biometria-component'             , require('./components/otros/Biometria.vue'        ).default);

/* SECCION DE BODEGA */
Vue.component('bodega-component'                , require('./components/bodega/Bodega.vue'          ).default);
Vue.component('ingreso-bodega-component'        , require('./components/bodega/Ingreso.vue'         ).default);
Vue.component('salida-bodega-component'         , require('./components/bodega/Salida.vue'          ).default);
Vue.component('informes-bodega-component'       , require('./components/bodega/Informes.vue'        ).default);

/* SECCION DE VALORIZACION */
Vue.component('valorizacion-component'          , require('./components/valorizacion/Valorizacion.vue'          ).default);
Vue.component('nueva-valorizacion-component'    , require('./components/valorizacion/Nueva_Valorizacion.vue'    ).default);
Vue.component('ver-valorizacion-component'      , require('./components/valorizacion/Ver_Valorizacion.vue'      ).default);
Vue.component('lista-valorizaciones-component'  , require('./components/valorizacion/Lista_Valorizaciones.vue'  ).default);


/*OTROS COMPONENTES*/
Vue.component('v-select', vSelect)

new Vue({
  render: function (h) { return h(App) },
}).$mount('#app')
