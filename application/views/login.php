<!doctype html>
<html lang="en">
  <head>
    <title>Login</title>
    <link rel="icon" href="./public/favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes">
    <link type="text/css" rel="stylesheet" href="./libraries/fontawesome/css/all.min.css">
    <link type="text/css" rel="stylesheet" href="./libraries/animate.css">
    <script src="./libraries/jquery.min.js"></script>
    <link type="text/css" rel="stylesheet" href="./libraries/bootstrap.min.css" />
    <link type="text/css" rel="stylesheet" href="./libraries/bootstrap-vue.min.css" />
    <link type="text/css" rel="stylesheet" href="./libraries/bootstrap-vue-icons.min.css" />
    <link type="text/css" rel="stylesheet" href="./libraries/estilos.css" />

    <!-- Load polyfills to support older browsers -->
    <script src="//polyfill.io/v3/polyfill.min.js?features=es2015%2CIntersectionObserver" crossorigin="anonymous"></script>

    <!-- Load Vue followed by BootstrapVue -->
    <script src="./libraries/vue.min.js"></script>
    <script src="./libraries/bootstrap-vue.min.js"></script>
    <script src="./libraries/bootstrap-vue-icons.min.js"></script>
    <script src="./libraries/axios.min.js"></script>
    <script src="./libraries/crypto-js.min.js"></script>

  </head>
  <body>
    <div id="app">
        <div class="container-fluid m-0 p-0 principal bg-dark">
        
            <div class="fondo-lg" :class="{'fondo1': fondo==1, 'fondo2': fondo==2, 'fondo3': fondo==3, 'fondo4': fondo==4}"></div>
            
            <div class="row justify-content-end m-0 p-0" style="min-height:100vh;"> 
            <div class="col-xl-6 col-lg-6 col-md-4 col-sm-12 p-0 text-white d-none d-md-block" style="margin-top: 15%;">
                <div class="container-fluid">
                <div class="row justify-content-end">
                    <div class="col-lg-6 col-sm-12 text-center p-0">
                    <h1 class="display-2 animated fadeIn"><i class="fas fa-users"></i></h1>
                    <h4 class="mr-4 ml-4 mb-0 animated fadeIn">RECURSOS HUMANOS</h4>  
                    <hr class="mt-4 mb-4 bg-white"/> 
                    <h6 class="animated fadeIn mt-2 mt-md-0 ml-2 mr-2"></h6> 
                    </div>
                </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6 col-md-4 col-sm-12 p-0 pt-3 text-white d-sm-block d-md-none fondo-sm" :class="{'fondo1': fondo==1, 'fondo2': fondo==2, 'fondo3': fondo==3, 'fondo4': fondo==4}">
                <div class="container-fluid">
                <div class="row justify-content-end pl-3 pr-3">
                    <div class="col-lg-6 col-sm-12 text-center p-0">
                    <h1 class="display-5 animated fadeIn "><i class="fas fa-users"></i></h1>
                    <h4 class="mr-4 ml-4 mb-0 animated fadeIn">RECURSOS HUMANOS</h4>  
                    <h6 class="animated fadeIn mt-2"></h6> 
                    </div>
                </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6 col-md-8 col-sm-12 p-3 text-center bg-white" style="min-height:100%;">
                <div class="container-fluid pb-4" style="height:100%;">
                    <b-overlay :show="cargando_inicio" rounded-pill class="d-inline-block btn-block">
                        <template #overlay>
                            <div class="text-center text-dark">
                                <b-icon icon="three-dots" font-scale="5" animation="cylon"></b-icon>
                                <p id="cancel-label">INGRESANDO . . .</p>
                            </div>
                        </template>
                        <div class="row justify-content-center pt-4" style="min-height: 70%;">
                        
                            <div class="col-lg-6 col-md-12 col-sm-12 mt-5">
                            <h4 class="mb-4">INGRESO</h4>

                            <input ref="rut" id="in_rut" maxlength="12" v-model="usuario" type="text" class="form-control shadow rounded-pill animated fadeIn pl-3 mb-4 alto" placeholder="RUT"
                            @keyup.enter="iniciarSesion()" @focus="marcaFoco('in_rut')" @blur="desmarcaFoco('in_rut')" @input="usuario = formateaRut(usuario)" 
                            :class="{'is-valid': verificaRut(usuario), 'is-invalid': !verificaRut(usuario) && usuario != ''}">

                            <!-- <input ref="rut" id="in_rut" v-model="usuario" type="text" class="form-control shadow rounded-pill animated fadeIn pl-3 mb-4 alto " placeholder="USUARIO"
                            @keyup.enter="iniciarSesion()" @focus="marcaFoco('in_rut')" @blur="desmarcaFoco('in_rut'); usuario=usuario.toLowerCase();" name="user"
                            :class="{'is-valid': usuario != '' && usuario != null, 'is-invalid': usuario == '' }"> -->

                            <div class="input-group mb-4 rounded-pill shadow animated fadeIn" id="clave" style="border:1px solid #ced4da;">
                                <input class="form-control border-0 rounded-pill-left alto pl-3" type="password" v-model="clave" id="in_clave" placeholder="CLAVE"
                                @keyup.enter="iniciarSesion()" 
                                @focus="marcaFoco('clave')" @blur="desmarcaFoco('clave')">
                                <span class="input-group-append">
                                <button class="btn bg-white border-0 rounded-pill-right" type="button">
                                    <i v-if="!clave_visible" @click="muestraClave()" class="far fa-eye fa-fw"></i>
                                    <i v-else @click="muestraClave()" class="far fa-eye-slash fa-fw"></i>
                                </button>
                                </span>
                            </div>
                            
                            <b-button variant="dark" class="btn-block shadow rounded-pill animated fadeIn alto" @click="iniciarSesion()"><i class="fas fa-sign-in-alt fs-fw mr-2"></i>INGRESAR</b-button>
                            
                            <hr class="mt-4 mb-4"/>  

                            </div>
                        </div>
                    </b-overlay>
                    <div class="row bg-white">
                        
                    </div>
                    <div class="row bg-white text-muted mt-5">
                        <div class="col text-center">
                        <p class="font-italic mb-1" style="font-size:14px;">Desarrollado por Inverluz S.A.</p>
                        <p class="font-italic" style="font-size:13px;">Copyright © 2021</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<script src="./libraries/Login.js"></script>

</html>
