<template>
    <div>
        <div class="row">
            <div class="col-sm-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Nuevo Turno</h4>
                    </div>
                    <div class="card-body">
                        <form action="" v-on:submit.prevent="grabarNuevo()">
                            <Input title="Fecha:" v-model="datos.fecha" type="date"></Input>
                            <Select title="Turno:" v-model="datos.horario">
                                <option value="1">TURNO 1</option>
                                <option value="2">TURNO 2</option>
                                <!-- <option value="2">TURNO 3</option> -->
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
                        <h4 class="card-title">Lista de Turnos</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Turno</th>
                                    <th>Fecha</th>
                                    <th>Editar</th>
                                    <!-- <th>Estado</th> -->
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="turno in table.data">
                                    <td>{{turno.descripcion}}</td>
                                    <td>{{turno.fecha}}</td>
                                    <td>
                                        <button @click="abrirEditar(turno.id)" class="btn-link-info">
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
                        <h5 class="modal-title">Editar turno</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" v-on:submit.prevent="grabarEditar()">
                            <Input title="Descripción:" v-model="turno_editar.descripcion" :error="errors_editar.descripcion"></Input>
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

import Select from '../../dragon-desing/dg-select.vue'
import Input from '../../dragon-desing/dg-input.vue'
export default {
    components:{
        Input,
        Select
    },
    data() {
        return {
            datos:this.initurno(),
            turno_editar: this.initurno(),
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
            axios.get(url_base+'/turno?page='+n)
            .then(response => {
                this.table = response.data;
            })
        },
        initurno(){
            this.errors={};
            return {
                fecha: null,
                horario: null,
            }
        },
        grabarNuevo(){
            axios.post(url_base+'/turno',this.datos)
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case "VALIDATION":
                        this.errors=respuesta.data;
                        break;
                    case "OK":
                        this.turno=this.initurno();
                        swal("", "Turno Registrado", "success");
                        this.listar();
                        break;
                    default:
                        break;
                }
            });
        },
        // actualizarEstado(id){
        //     axios.post(url_base+'/turno/'+id+'/estado')
        //     .then(response => {
        //         var respuesta=response.data;
        //         switch (respuesta.status) {
        //             case "OK":
        //                 swal("", "Estado Actualizado", "success");
        //                 this.listar();
        //                 break;
        //             default:
        //                 break;
        //         }
        //     });
        // },
        grabarEditar(){
            axios.post(url_base+'/turno/'+this.turno_editar.id+'?_method=PUT',this.turno_editar)
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case "VALIDATION":
                        this.errors_editar=respuesta.data;
                        break;
                    case "OK":
                        this.turno_editar=this.initurno();
                        this.listar();
                        swal("", "Turno Actualizado", "success");
                        $('#modal-editar').modal('hide');
                        break;
                    default:
                        break;
                }
            });
        },
        abrirEditar(id){
            axios.get(url_base+'/turno/'+id)
            .then(response => {
                this.turno_editar = response.data;
            })
            $('#modal-editar').modal();
        }
    },
}
</script>