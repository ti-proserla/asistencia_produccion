<template>
    <div>
        <div class="row">
            <div class="col-sm-6">
                <form v-if="iniciado==null"  v-on:submit.prevent="inicializar">
                    INICIALIZAR
                    <input id="txt-cod_barras" type="text" v-model="cod_barras" ref="cod_barras_ref">
                    <button ref="ejecutar" type="submit">Guardar</button>
                </form>
                <form v-else  v-on:submit.prevent="guardar">
                    CONTEO
                    <input id="txt-cod_barras" type="text" v-model="cod_barras" ref="cod_barras_ref">
                    <button  type="submit">Guardar</button>
                </form>
            </div>
            <div class="col-sm-6" v-if="active">
                <v-quagga frecuency="4" :onDetected="logIt"  :readerTypes="['code_128_reader','ean_reader','codabar_reader']"></v-quagga>
            </div>
        </div>

    </div>
</template>
<style>
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
<script>
export default {
    data() {
        return {
            info: null,
            cod_barras: null,
            iniciado: null,
            detecteds: [],
            active: true
        }
    },
    mounted() {
        // console.log(this.$refs.cod_barras_ref.focus);
      
      this.seguroFocus();
    },
    methods: {
        logIt (data) {
            if (this.active==true) {
                this.active=false;
                console.log( data)
                setTimeout(() => {
                    this.active=true;
                }, 200);
            }
        },
        seguroFocus(){
            var t=this;
            setTimeout(() => {
                t.$refs.cod_barras_ref.focus();
                t.seguroFocus();    
            }, 1000);
        },
        inicializar(){
            this.iniciado=this.cod_barras;
            this.cod_barras=null;
        },
        guardar(){
            var cod_barras_paso=this.cod_barras;
            this.cod_barras=null;
            axios
            .post('http://127.00.1:8000/api/conteo',{ cod_barras: cod_barras_paso, configuracion: this.iniciado})
            .then(response => {
                app.info = response.data;
            })
        }
    },
}
</script>