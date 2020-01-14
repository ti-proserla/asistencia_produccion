<template>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Configuraciones</h4>
                </div>
                <div class="card-body">
                    <hr>
                    <h6>Sincronizaciones</h6>
                    <button @click="sincronizarTodo()" class="btn btn-danger">Sincronizar Dependencias</button>
                    <hr>
                    <h6>Parametros Globales</h6>
                    <div class="row">
                        <div class="col-6">
                            <label for="">Tiempo entre Marcas:</label>
                        </div>
                        <div class="col-6">
                            <div class="input-group mb-2">
                                <input type="number" class="form-control" v-model="configuraciones.tiempo_entre_marcas">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">Min</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <label for="">Hora de Cierre de Turno:</label>
                        </div>
                        <div class="col-6">
                            <div class="input-group mb-2">
                                <input type="number" class="form-control" v-model="configuraciones.hora_cierre_turno">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">24H</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mb-2">
                            <label for="">Codigo de Fundos:</label>
                        </div>
                        <div class="col-6 mb-2">
                            <!-- <div class="input-group mb-2"> -->
                            <input type="text" class="form-control" v-model="configuraciones.ccosto">
                                <!-- <div class="input-group-prepend">
                                    <div class="input-group-text">24H</div>
                                </div>
                            </div> -->
                        </div>
                        <div class="col-6 mb-2">
                            <label for="">Codigo de Actividades:</label>
                        </div>
                        <div class="col-6 mb-2">
                            <!-- <div class="input-group mb-2"> -->
                            <input type="text" class="form-control" v-model="configuraciones.actividad">
                                <!-- <div class="input-group-prepend">
                                    <div class="input-group-text">24H</div>
                                </div>
                            </div> -->
                        </div>
                        <div class="col-12 text-right">
                            <button class="btn btn-primary" @click="actualizar()">Guardar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</template>
<script>
export default {
    data() {
        return {
            configuraciones:[]
        }
    },
    mounted() {
        this.listar();
    },
    methods: {
        listar(){
            axios.get(url_base+'/configuracion')
            .then(response => {
                this.configuraciones = response.data;
            })
        },
        actualizar(){
            axios.post(url_base+'/configuracion/a?_method=PUT',this.configuraciones)
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case "OK":
                        this.listar();
                        swal("", "Parametro Actualizado", "success");
                        break;
                    default:
                        break;
                }
            });
        },
        sincronizarTodo(){
            axios.get(url_base+'/sincronizar/proceso')
            .then(response => {
                
            });
            axios.get(url_base+'/sincronizar/area')
            .then(response => {
                axios.get(url_base+'/sincronizar/labor')
                .then(response => {
                    // this.listar();
                    // // this.table = response.data;
                });
            })
        }
    },
}
</script>