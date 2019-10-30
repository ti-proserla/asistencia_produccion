<template>
    <div class="container">
        <h3 class="my-3 text-center">CAPTURADOR CON LECTOR DE CODIGOS</h3>
        <div class="col-lg-8 offset-lg-2">
            <div class="card">
                <div class="card-header">
                    <h4 v-if="iniciado==null" class="text-center">INICIALIZAR</h4>
                    <h4 v-else class="text-center">CONTEO</h4>
                </div>
                <div class="card-body">
                    <div class="col-sm-6" v-if="active">
                        <div class="content-camara">
                            <button v-if="activarCaptura==true" class="btn-activar-camara active"></button>
                            <button @click="activeScanner()" v-else class="btn-activar-camara"></button>
                            <v-quagga frecuency="4" :onDetected="logIt"  :readerTypes="['code_128_reader','ean_reader','codabar_reader']"></v-quagga>
                        </div>
                    </div>
                    <form v-if="iniciado==null"  v-on:submit.prevent="inicializar">
                        <div class="input-group mb-3">
                            <input id="txt-cod_barras" type="text" v-model="cod_barras" ref="cod_barras_ref" placeholder="Leer Codigo de barras" class="form-control">
                            <div class="input-group-append">
                                <button class="btn btn-danger" type="submit" ref="ejecutar">Guardar</button>
                            </div>

                        </div>
                    </form>
                    <form v-else  v-on:submit.prevent="guardar">
                        <div class="row">
                            <div class="col-sm-4">
                                <p class="mb-1"><label for="">Linea: {{ iniciado.substring(0,2) }}</label></p>
                                <p class="mb-1"><label for="">Producto: {{ iniciado.substring(2,4) }}</label></p>
                            </div>
                            <div class="col-sm-8">
                                <div class="input-group mb-3">
                                    <!-- <input id="txt-cod_barras" type="text" v-model="cod_barras" ref="cod_barras_ref" placeholder="Leer Codigo de barras" class="form-control"> -->
                                    <div class="input-group-append">
                                        <button class="btn btn-danger" type="submit" ref="ejecutar">Guardar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            info: null,
            cod_barras: null,
            iniciado: null,
            detecteds: [],
            active: true, //Parpadeo de camara
            activarCaptura: true,
        }
    },
    mounted() {
        // console.log(this.$refs.cod_barras_ref.focus);
        // this.seguroFocus();
    },
    methods: {
        activeScanner(){
            this.activarCaptura=true;
        },
        logIt (data) {
            //Desactiva la lectura y registro
            if (this.activarCaptura==true) {
                this.activarCaptura=false;
                this.active=false;
                this.cod_barras=data.codeResult.code;
                this.$refs.ejecutar.click();
                window.navigator.vibrate(200);
                setTimeout(() => {
                    this.active=true;
                }, 300);
            }
        },
        // seguroFocus(){
        //     var t=this;
        //     setTimeout(() => {
        //         t.$refs.cod_barras_ref.focus();
        //         t.seguroFocus();    
        //     }, 1000);
        // },
        inicializar(){
            if (this.cod_barras.length==4) {
                this.iniciado=this.cod_barras;
            }else{
                alert('Configuración no valida');
            }
            this.cod_barras=null;
        },
        guardar(){
            var cod_barras_paso=this.cod_barras;
            this.cod_barras=null;
            axios
            .post(url_base+'/conteo',{ cod_barras: cod_barras_paso, configuracion: this.iniciado})
            .then(response => {
                this.info = response.data;
            })
        }
    },
}
</script>
<style>
    .content-camara{
        position: relative;
    }
    .btn-activar-camara{
        height: 30px;
        width: 30px;
        position: absolute;
        top: 5px;
        right: 5px;
        background-color: red;
        z-index: 10;
        color: #fff;
        font-weight: 700;
        border: 1px solid #fff;
        border-radius: 50%;
    }
    .btn-activar-camara.active{
        background-color: green;
    }
    .scanner canvas{
        display: none;
    }
    .scanner video{
        position: relative!important;
        width: 100%;
    }
    .scanner{
        display: block;

    }
</style>
