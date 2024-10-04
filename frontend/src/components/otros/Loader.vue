<template>
    <!-- Modal LOADER-->
    <b-modal v-model="cargando_pagina" hide-header hide-footer centered no-close-on-esc no-close-on-backdrop size="sm" 
        :body-text-variant="'primary'" content-class="bg-transparent border-0" :body-bg-variant="'transparent'">
        <div class="text-center text-white my-2">
            <div class="spinner-border" style="width: 4rem; height: 4rem;" role="status">
                
            </div><br>
            <label class="text-white">CARGANDO...</label>
        </div>
    </b-modal>
</template>

<script>
    export default {
        props: ['cargando_pagina'],
        data(){
            return{
            }
        },
        mounted() {
        },
        methods: {
        }
    }
</script>