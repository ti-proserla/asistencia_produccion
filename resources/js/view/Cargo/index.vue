<template>
    <div>
        <div class="row">
            <div class="col-sm-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Nuevo cargo</h4>
                    </div>
                    <div class="card-body">
                        <form action="" v-on:submit.prevent="grabarNuevo()">
                            <Input title="Nombre:" v-model="cargo.nom_cargo" :error="errors.nom_cargo"></Input>
                            <Input title="Centro Costo:" v-model="cargo.proceso_id"></Input>
                            <Input title="Actividad:" v-model="cargo.area_id"></Input>
                            <Input title="Labor:" v-model="cargo.labor_id"></Input>
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
                        <h4 class="card-title">Lista de cargos</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Descripcion</th>
                                    <th>Editar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="cargo in table.data">
                                    <td>{{cargo.nom_cargo}}</td>
                                    <td>
                                        <button @click="abrirEditar(cargo.id)" class="btn-link-info">
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
                        <h5 class="modal-title">Editar cargo</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" v-on:submit.prevent="grabarEditar()">
                            <Input title="Nombre:" v-model="cargo_editar.nom_cargo" :error="errors_editar.nom_cargo"></Input>
                            <Input title="Centro Costo:" v-model="cargo_editar.proceso_id"></Input>
                            <Input title="Actividad:" v-model="cargo_editar.area_id"></Input>
                            <Input title="Labor:" v-model="cargo_editar.labor_id"></Input>
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
            cargo: this.inicargo(), //datos de logeo
            cargo_editar: this.inicargo(),
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
            axios.get(url_base+'/cargo?page='+n)
            .then(response => {
                this.table = response.data;
            })
        },
        inicargo(){
            this.errors={};
            return {
                nom_cargo: null,
            }
        },
        grabarNuevo(){
            axios.post(url_base+'/cargo',this.cargo)
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case "VALIDATION":
                        this.errors=respuesta.data;
                        break;
                    case "OK":
                        this.cargo=this.inicargo();
                        swal("", "cargo Registrado", "success");
                        this.listar();
                        break;
                    default:
                        break;
                }
            });
        },
        actualizarEstado(id){
            axios.post(url_base+'/cargo/'+id+'/estado')
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
            axios.post(url_base+'/cargo/'+this.cargo_editar.id+'?_method=PUT',this.cargo_editar)
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case "VALIDATION":
                        this.errors_editar=respuesta.data;
                        break;
                    case "OK":
                        this.cargo_editar=this.inicargo();
                        this.listar();
                        swal("", "cargo Actualizado", "success");
                        $('#modal-editar').modal('hide');
                        break;
                    default:
                        break;
                }
            });
        },
        abrirEditar(id){
            axios.get(url_base+'/cargo/'+id)
            .then(response => {
                this.cargo_editar = response.data;
            })
            $('#modal-editar').modal();
        }
    },
}
</script>