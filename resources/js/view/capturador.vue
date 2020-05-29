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
                    <form v-if="iniciado==null"  v-on:submit.prevent="inicializar">
                        <div class="input-group mb-3">
                            <input id="txt-cod_barras" type="text" v-model="cod_barras" ref="cod_barras_ref" placeholder="Leer Codigo de barras" class="form-control">
                            <div class="input-group-append">
                                <button class="btn btn-danger" type="submit">Guardar</button>
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
                                    <input id="txt-cod_barras" type="text" v-model="cod_barras" ref="cod_barras_ref" placeholder="Leer Codigo de barras" class="form-control">
                                    <div class="input-group-append">
                                        <button class="btn btn-danger" type="submit">Guardar</button>
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
        }
    },
    mounted() {
        console.log(this.$refs.cod_barras_ref.focus);
        this.seguroFocus();
    },
    methods: {
        seguroFocus(){
            var t=this;
            setTimeout(() => {
                t.$refs.cod_barras_ref.focus();
                t.seguroFocus();    
            }, 1000);
        },
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