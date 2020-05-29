<template>
    <div>
        <div class="row">
            <div class="col-sm-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Nuevo Rol</h4>
                    </div>
                    <div class="card-body">
                        <form action="" v-on:submit.prevent="grabarNuevo()">
                            <Input title="Nombre:" v-model="rol.nombre_rol" :error="errors.nombre_rol"></Input>
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
                        <h4 class="card-title">Lista de rols</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Descripcion</th>
                                    <th>Editar</th>
                                    <th>Privilegios</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="rol in table.data">
                                    <td>{{rol.nombre_rol}}</td>
                                    <td>
                                        <button @click="abrirEditar(rol.id)" class="btn-link-info">
                                            <i class="material-icons">create</i>
                                        </button>
                                    </td>
                                    <td>
                                        <button @click="abrirModulos(rol.id)" class="btn-link-info">
                                            <i class="material-icons">assignment_ind</i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="pagination">
                            <a v-for="n in table.last_page" :class="{active: table.current_page==n}" @click="listar(n)">{{n}}</a>
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
                        <h5 class="modal-title">Editar rol</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" v-on:submit.prevent="grabarEditar()">
                            <Input title="Nombre:" v-model="rol_editar.nombre_rol" :error="errors_editar.nombre_rol"></Input>
                            <div class="text-center">
                                <button type="submit" class="btn btn-success">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!--Fin Modal Editar-->
        <div id="modal-privilegios" class="modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Privilegios</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- <div id='example-3'> -->
                            <div v-for="modulo in modulos">
                                <input  type="checkbox" :value="modulo.id" v-model="rol_modulos.modulos">
                                <label>{{modulo.nombre_modulo}}</label>
                            </div>
                            <br>
                            <div class="text-right">
                                <button @click="guardarModulos()" class="btn-primary btn"> 
                                    Guardar
                                </button>
                            </div>
                        <!-- </div> -->
                    </div>
                </div>
            </div>
        </div>
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
            modulos: [],
            rol: this.inirol(), //datos de logeo
            rol_editar: this.inirol(),
            errors: {}, //datos de errores
            errors_editar: {}, //datos de errores
            //Datos de Tabla:
            table:{
                data:[]
            },
            url: null,
            rol_modulos: this.initRolModulos()
        }
    },
    mounted() {
        this.listar();
        this.listarModulo();
    },
    methods: {
        initRolModulos(){
            return {
                id: 0,
                modulos: []
            }
        },
        abrirModulos(id){
            axios.get(url_base+'/rol/'+id+'/modulos')
            .then(response => {
                this.rol_modulos.modulos = [...new Set(response.data)] ;
            })
            this.rol_modulos.id=id;
            $('#modal-privilegios').modal();
        },
        guardarModulos(){
            axios.post(url_base+'/rol/'+this.rol_modulos.id+'/modulos',this.rol_modulos)
            .then(response => {
                var respuesta=response.data;
                if (respuesta.status=="OK") {
                    $('#modal-privilegios').modal('hide');
                    swal("", respuesta.data, "success");
                }else{
                    swal("", respuesta.data, "warning");
                }
            });
        },
        listar(n=this.table.from){
            axios.get(url_base+'/rol?page='+n)
            .then(response => {
                this.table = response.data;
            })
        },
        listarModulo(){
            axios.get(url_base+'/modulo?all=true')
            .then(response => {
                this.modulos = response.data;
            });
        },
        inirol(){
            this.errors={};
            return {
                nombre_rol: null,
            }
        },
        grabarNuevo(){
            axios.post(url_base+'/rol',this.rol)
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case "VALIDATION":
                        this.errors=respuesta.data;
                        break;
                    case "OK":
                        this.rol=this.inirol();
                        swal("", "rol Registrado", "success");
                        this.listar();
                        break;
                    default:
                        break;
                }
            });
        },
        actualizarEstado(id){
            axios.post(url_base+'/rol/'+id+'/estado')
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
            axios.post(url_base+'/rol/'+this.rol_editar.id+'?_method=PUT',this.rol_editar)
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case "VALIDATION":
                        this.errors_editar=respuesta.data;
                        break;
                    case "OK":
                        this.rol_editar=this.inirol();
                        this.listar();
                        swal("", "rol Actualizado", "success");
                        $('#modal-editar').modal('hide');
                        break;
                    default:
                        break;
                }
            });
        },
        abrirEditar(id){
            axios.get(url_base+'/rol/'+id)
            .then(response => {
                this.rol_editar = response.data;
            })
            $('#modal-editar').modal();
        }
    },
}
</script>