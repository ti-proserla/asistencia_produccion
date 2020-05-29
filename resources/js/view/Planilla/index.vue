<template>
    <div>
        <div class="row">
            <div class="col-sm-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Nuevo planilla</h4>
                    </div>
                    <div class="card-body">
                        <form action="" v-on:submit.prevent="grabarNuevo()">
                            <Input title="Nombre:" v-model="planilla.nom_planilla" :error="errors.nom_planilla"></Input>
                            <Input title="Tiempo entre Marcas (m):" v-model="planilla.tiempo_entre_marcas" :error="errors.tiempo_entre_marcas"></Input>
                            <Input title="Limite de Salida (H):" v-model="planilla.salida" :error="errors.salida"></Input>
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
                        <h4 class="card-title">Lista de planillas</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Descripcion</th>
                                    <th>T. Entre Marcas</th>
                                    <th>Limite Salida</th>
                                    <th>Editar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="planilla in table.data">
                                    <td>{{planilla.nom_planilla}}</td>
                                    <td>{{planilla.tiempo_entre_marcas}}</td>
                                    <td>{{planilla.salida}}</td>
                                    <td>
                                        <button @click="abrirEditar(planilla.id)" class="btn-link-info">
                                            <i class="material-icons">create</i>
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
                        <h5 class="modal-title">Editar planilla</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" v-on:submit.prevent="grabarEditar()">
                            <Input title="Nombre:" v-model="planilla_editar.nom_planilla" :error="errors_editar.nom_planilla"></Input>
                            <Input title="Tiempo entre Marcas (m):" v-model="planilla_editar.tiempo_entre_marcas" :error="errors_editar.tiempo_entre_marcas"></Input>
                            <Input title="Limite de Salida (H):" v-model="planilla_editar.salida" :error="errors_editar.salida"></Input>
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
            planilla: this.iniplanilla(), //datos de logeo
            planilla_editar: this.iniplanilla(),
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
            axios.get(url_base+'/planilla?page='+n)
            .then(response => {
                this.table = response.data;
            })
        },
        iniplanilla(){
            this.errors={};
            return {
                nom_planilla: null,
            }
        },
        grabarNuevo(){
            axios.post(url_base+'/planilla',this.planilla)
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case "VALIDATION":
                        this.errors=respuesta.data;
                        break;
                    case "OK":
                        this.planilla=this.iniplanilla();
                        swal("", "planilla Registrado", "success");
                        this.listar();
                        break;
                    default:
                        break;
                }
            });
        },
        actualizarEstado(id){
            axios.post(url_base+'/planilla/'+id+'/estado')
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
            axios.post(url_base+'/planilla/'+this.planilla_editar.id+'?_method=PUT',this.planilla_editar)
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case "VALIDATION":
                        this.errors_editar=respuesta.data;
                        break;
                    case "OK":
                        this.planilla_editar=this.iniplanilla();
                        this.listar();
                        swal("", "planilla Actualizado", "success");
                        $('#modal-editar').modal('hide');
                        break;
                    default:
                        break;
                }
            });
        },
        abrirEditar(id){
            axios.get(url_base+'/planilla/'+id)
            .then(response => {
                this.planilla_editar = response.data;
            })
            $('#modal-editar').modal();
        }
    },
}
</script>