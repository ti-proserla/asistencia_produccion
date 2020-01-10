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
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nombre Configuración</th>
                                <th>Parametro</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="conf in configuraciones">
                                <td>{{ conf.nombre }}</td>
                                <td><input type="text" v-model="conf.parametro"></td>
                                <td><button @click="actualizar(conf.id,conf.parametro)">Actualizar</button></td>
                            </tr>
                        </tbody>
                    </table>
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
        actualizar(id,parametro){
            axios.post(url_base+'/configuracion/'+id+'?_method=PUT',{ parametro: parametro })
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