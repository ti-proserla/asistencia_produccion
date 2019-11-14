<template>
    <div>
        <div class="row">
            <div class="col-sm-5">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Nuevo Operador</h4>
                    </div>
                    <div class="card-body">
                        <form action="" v-on:submit.prevent="grabarNuevo()">
                            <Input title="DNI:" v-model="operador.dni" :error="errors.dni"></Input>
                            <Input title="Nombre:" v-model="operador.nom_operador" :error="errors.nom_operador"></Input>
                            <Input title="Apellido:" v-model="operador.ape_operador" :error="errors.ape_operador"></Input>
                            <div class="text-center">
                                <button type="submit" class="btn btn-success">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-sm-7">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Lista de Operadores</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>DNI</th>
                                    <th>Nombres y Apellidos</th>
                                    <th>Editar</th>
                                    <th>Foto Check</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="operador in table.data">
                                    <td>{{operador.dni}}</td>
                                    <td>{{operador.nom_operador}} {{operador.ape_operador}}</td>
                                    <td>
                                        <button @click="abrirEditar(operador.dni)" class="btn btn-info"></button>
                                    </td>
                                    <td>
                                        <button @click="verFotoCheck(operador.dni)" class="btn btn-warning"></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="pagination">
                            <a v-for="n in table.last_page" :class="{active: table.from==n}">{{n}}</a>
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
                        <h5 class="modal-title">Editar Operador</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" v-on:submit.prevent="grabarEditar()">
                            <Input title="DNI:" v-model="operador_editar.dni" :error="errors_editar.dni"></Input>
                            <Input title="Nombre:" v-model="operador_editar.nom_operador" :error="errors_editar.nom_operador"></Input>
                            <Input title="Apellido:" v-model="operador_editar.ape_operador" :error="errors_editar.ape_operador"></Input>
                            <div class="text-center">
                                <button type="submit" class="btn btn-success">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!--Fin Modal Editar-->
        <div id="modal-fotocheck" class="modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Modal title</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" style="text-align: center;">
                        <iframe :src="url" frameborder="0" width="200" height="300"></iframe>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary">Save changes</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import Input from '../dragon-desing/dg-input.vue'
export default {
    components:{
        Input
    },
    data() {
        return {
            operador: this.iniOperador(), //datos de logeo
            operador_editar: this.iniOperador(),
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
        listar(){
            axios.get(url_base+'/operador')
            .then(response => {
                this.table = response.data;
            })
        },
        iniOperador(){
            this.errors={};
            return {
                dni: null,
                nom_operador: null,
                ape_operador:null
            }
        },
        verFotoCheck(id){
            this.url=url_base+'/../fotocheck/'+id;
            $('#modal-fotocheck').modal();
        },
        grabarNuevo(){
            axios.post(url_base+'/operador',this.operador)
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case "VALIDATION":
                        this.errors=respuesta.data;
                        break;
                    case "OK":
                        this.operador=this.iniOperador();
                        swal("", "Operador Registrado", "success");
                        this.listar();
                        break;
                    default:
                        break;
                }
            });
        },
        grabarEditar(){
            axios.post(url_base+'/operador/'+this.operador_editar.dni+'?_method=PUT',this.operador_editar)
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case "VALIDATION":
                        this.errors_editar=respuesta.data;
                        break;
                    case "OK":
                        this.operador_editar=this.iniOperador();
                        this.listar();
                        break;
                    default:
                        break;
                }
            });
        },
        abrirEditar(id){
            axios.get(url_base+'/operador/'+id)
            .then(response => {
                this.operador_editar = response.data;
            })
            $('#modal-editar').modal();
        }
    },
}
</script>