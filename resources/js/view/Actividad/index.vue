<template>
    <div>
        <div class="row">
            <div class="col-sm-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Nuevo Actividad</h4>
                    </div>
                    <div class="card-body">
                        <form action="" v-on:submit.prevent="grabarNuevo()">
                            <Input title="Codigo:" v-model="actividad.codigo" :error="errors.codigo"></Input>
                            <Input title="Nombre:" v-model="actividad.nom_actividad" :error="errors.nom_actividad"></Input>
                            <div class="text-center">
                                <button type="submit" class="btn btn-success">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Lista de Actividades</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Descripcion</th>
                                    <th>Editar</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="actividad in table.data">
                                    <td>{{actividad.codigo}}</td>
                                    <td>{{actividad.nom_actividad}}</td>
                                    <td>
                                        <button @click="abrirEditar(actividad.id)" class="btn btn-info">
                                            <i class="material-icons">create</i>
                                        </button>
                                    </td>
                                    <td>
                                        <button v-if="actividad.estado=='0'" @click="actualizarEstado(actividad.id)" class="btn btn-info">
                                            <i class="material-icons">radio_button_checked</i>
                                        </button>
                                        <button v-else @click="actualizarEstado(actividad.id)" class="btn btn-gray">
                                            <i class="material-icons">radio_button_unchecked</i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="pagination">
                            <a v-for="n in table.last_page" :class="{active: table.from==n}" @click="listar(n)">{{n}}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--Modal Editar-->
        <div id="modal-editar" class="modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Actividad</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" v-on:submit.prevent="grabarEditar()">
                            <Input title="Codigo:" v-model="actividad_editar.codigo" :error="errors_editar.codigo"></Input>
                            <Input title="Nombre:" v-model="actividad_editar.nom_actividad" :error="errors_editar.nom_actividad"></Input>
                            <div class="text-center">
                                <button type="submit" class="btn btn-success">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!--Fin Modal Editar-->
    </div>
</template>
<script>
import Input from '../../dragon-desing/dg-input.vue'
export default {
    components:{
        Input
    },
    data() {
        return {
            actividad: this.iniactividad(), //datos de logeo
            actividad_editar: this.iniactividad(),
            errors: {}, //datos de errores
            errors_editar: {}, //datos de errores
            //Datos de Tabla:
            table:{
                data:[]
            },
            url: null
        }
    },
    mounted() {
        this.listar();
    },
    methods: {
        listar(n=this.table.from){
            axios.get(url_base+'/actividad?page='+n)
            .then(response => {
                this.table = response.data;
            })
        },
        iniactividad(){
            this.errors={};
            return {
                codigo: null,
                nom_actividad: null,
            }
        },
        grabarNuevo(){
            axios.post(url_base+'/actividad',this.actividad)
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case "VALIDATION":
                        this.errors=respuesta.data;
                        break;
                    case "OK":
                        this.actividad=this.iniactividad();
                        swal("", "actividad Registrado", "success");
                        this.listar();
                        break;
                    default:
                        break;
                }
            });
        },
        actualizarEstado(id){
            axios.post(url_base+'/actividad/'+id+'/estado')
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case "OK":
                        swal("", "Estado Actualizado", "success");
                        this.listar();
                        break;
                    default:
                        break;
                }
            });
        },
        grabarEditar(){
            axios.post(url_base+'/actividad/'+this.actividad_editar.id+'?_method=PUT',this.actividad_editar)
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case "VALIDATION":
                        this.errors_editar=respuesta.data;
                        break;
                    case "OK":
                        this.actividad_editar=this.iniactividad();
                        this.listar();
                        swal("", "actividad Actualizado", "success");
                        $('#modal-editar').modal('hide');
                        break;
                    default:
                        break;
                }
            });
        },
        abrirEditar(id){
            axios.get(url_base+'/actividad/'+id)
            .then(response => {
                this.actividad_editar = response.data;
            })
            $('#modal-editar').modal();
        }
    },
}
</script>