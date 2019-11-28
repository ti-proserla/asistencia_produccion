<template>
    <div>
        <div class="row">
            <div class="col-sm-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Nueva Labor</h4>
                    </div>
                    <div class="card-body">
                        <form action="" v-on:submit.prevent="grabarNuevo()">
                            <Input title="Codigo:" v-model="labor.codigo" :error="errors.codigo"></Input>
                            <Input title="Nombre:" v-model="labor.nom_labor" :error="errors.nom_labor"></Input>
                            <Select title="Area:" v-model="labor.area_id" :error="errors.area_id">
                                <option value=""></option>
                                <option v-for="area in areas" :value="area.id">{{ area.nom_area }}</option>
                            </Select>
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
                        <h4 class="card-title">Lista de labores</h4>
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
                                <tr v-for="labor in table.data">
                                    <td>{{labor.codigo}}</td>
                                    <td>{{labor.nom_labor}}</td>
                                    <td>
                                        <button @click="abrirEditar(labor.id)" class="btn-link-info">
                                            <i class="material-icons">create</i>
                                        </button>
                                    </td>
                                    <td>
                                        <button v-if="labor.estado=='0'" @click="actualizarEstado(labor.id)" class="btn-link-info">
                                            <i class="material-icons">radio_button_checked</i>
                                        </button>
                                        <button v-else @click="actualizarEstado(labor.id)" class="btn-link-gray">
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
                        <h5 class="modal-title">Editar labor</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" v-on:submit.prevent="grabarEditar()">
                            <Input title="Codigo:" v-model="labor_editar.codigo" :error="errors_editar.codigo"></Input>
                            <Input title="Nombre:" v-model="labor_editar.nom_labor" :error="errors_editar.nom_labor"></Input>
                            <Select title="Area:" v-model="labor_editar.area_id" :error="errors_editar.area_id">
                                <option v-for="area in areas" :value="area.id">{{ area.nom_area }}</option>
                            </Select>
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
import Select from '../../dragon-desing/dg-select.vue'
export default {
    components:{
        Input,
        Select
    },
    data() {
        return {
            areas: [],
            labor: this.inilabor(), //datos de logeo
            labor_editar: this.inilabor(),
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
        this.listarAreas();
    },
    methods: {
        listarAreas(){
            axios.get(url_base+'/area?all=true')
            .then(response => {
                this.areas = response.data;
            })
        },
        listar(n=this.table.from){
            axios.get(url_base+'/labor?page='+n)
            .then(response => {
                this.table = response.data;
            })
        },
        inilabor(){
            this.errors={};
            return {
                codigo: null,
                nom_labor: null,
            }
        },
        grabarNuevo(){
            axios.post(url_base+'/labor',this.labor)
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case "VALIDATION":
                        this.errors=respuesta.data;
                        break;
                    case "OK":
                        this.labor=this.inilabor();
                        swal("", "labor Registrado", "success");
                        this.listar();
                        break;
                    default:
                        break;
                }
            });
        },
        actualizarEstado(id){
            axios.post(url_base+'/labor/'+id+'/estado')
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
            axios.post(url_base+'/labor/'+this.labor_editar.id+'?_method=PUT',this.labor_editar)
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case "VALIDATION":
                        this.errors_editar=respuesta.data;
                        break;
                    case "OK":
                        this.labor_editar=this.inilabor();
                        this.listar();
                        swal("", "labor Actualizado", "success");
                        $('#modal-editar').modal('hide');
                        break;
                    default:
                        break;
                }
            });
        },
        abrirEditar(id){
            axios.get(url_base+'/labor/'+id)
            .then(response => {
                this.labor_editar = response.data;
            })
            $('#modal-editar').modal();
        }
    },
}
</script>