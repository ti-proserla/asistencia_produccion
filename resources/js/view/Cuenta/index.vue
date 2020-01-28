<template>
    <div>
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Nueva Cuenta</h4>
                    </div>
                    <div class="card-body">
                        <form action="" v-on:submit.prevent="grabarNuevo()">
                            <label for="" hidden>{{consulta}}</label>
                            <Input title="Nombre:" v-model="cuenta.nombre" :error="errors.nombre"></Input>
                            <Input title="Apellido:" v-model="cuenta.apellido" :error="errors.apellido"></Input>
                            <Input title="usuario:" v-model="cuenta.usuario" :error="errors.usuario"></Input>
                            <Input title="password:" v-model="cuenta.password" :error="errors.password"></Input>
                            <Select title="Rol:" v-model="cuenta.rol">
                                <option value="COMUN">COMUN</option>
                                <option value="ADMINISTRADOR">ADMINISTRADOR</option>
                            </Select>
                            <div class="text-center">
                                <button type="submit" class="btn btn-success mt-3">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Lista de cuentas</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-2">
                                <label for="" class="my-3">Buscar: </label>
                            </div>
                            <div class="col-8">
                                <input @keyup="listar()" class="form-control" v-model="search"></input>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Nombres y Apellidos</th>
                                        <th>Editar</th>
                                        <th>Privilegios</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="cuenta in table.data">
                                        <td>{{cuenta.nombre}} {{cuenta.apellido}}</td>
                                        <td>
                                            <button @click="abrirEditar(cuenta.id)" class="btn-link-info">
                                                <i class="material-icons">create</i>
                                            </button>
                                        </td>
                                        <td>
                                            <button @click="abrirPrivilegios(cuenta.id)" class="btn-link-info">
                                                <i class="material-icons">assignment_ind</i>
                                            </button>
                                        </td>
                                        <td>
                                            <button v-if="cuenta.estado=='0'" @click="actualizarEstado(cuenta.id)" class="btn-link-info">
                                                <i class="material-icons">radio_button_checked</i>
                                            </button>
                                            <button v-else @click="actualizarEstado(cuenta.id)" class="btn-link-gray">
                                                <i class="material-icons">radio_button_unchecked</i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="pagination">
                            <a v-for="n in table.last_page" :class="{active: table.from==n}" @click="listar(n)">{{n}}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
                            <div v-for="fundo in fundos">
                                <input  type="checkbox" :value="fundo.id" v-model="cuenta_privilegios.privilegios">
                                <label>{{fundo.nom_fundo}}</label>
                            </div>
                            <br>
                            <div class="text-right">
                                <button @click="guardarPrivilegios()" class="btn-primary btn"> 
                                    Guardar
                                </button>
                            </div>
                        <!-- </div> -->
                    </div>
                </div>
            </div>
        </div>
        <!--Modal Editar-->
        <div id="modal-editar" class="modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar cuenta</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" v-on:submit.prevent="grabarEditar()">
                            <Input title="Nombre:" v-model="cuenta_editar.nombre" :error="errors_editar.nombre"></Input>
                            <Input title="Apellido:" v-model="cuenta_editar.apellido" :error="errors_editar.apellido"></Input>
                            <Select title="Rol:" v-model="cuenta_editar.rol">
                                <option value="COMUN">COMUN</option>
                                <option value="ADMINISTRADOR">ADMINISTRADOR</option>
                            </Select>
                            <div class="text-center">
                                <button type="submit" class="btn btn-success">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
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
            fundos:[],
            search: null,
            cuenta: this.iniCuenta(), //datos de logeo
            cuenta_editar: this.iniCuenta(),
            errors: {}, //datos de errores
            errors_editar: {}, //datos de errores
            //Datos de Tabla:
            table:{
                data:[]
            },
            url: null,
            myCroppa2: {}, 
            croppaConfig: {
                horizontal:  225,
                vertical:  300 ,
                quality: 2
            },
            cuenta_privilegios:{
                cuenta_id: null,
                privilegios: []
            }
        }
    },
    computed: {
        consulta(){
            if (this.cuenta.dni!=null) {
                console.log(this.cuenta);
                if (this.cuenta.dni.length==8) {
                    axios.get(url_base+'/jne/dni/'+this.cuenta.dni)
                    .then(response => {
                        var respuesta=response.data;
                        switch (respuesta.status) {
                            case "INFO":
                                swal({
                                    text:respuesta.data, 
                                    icon: "info",
                                    buttons: true,
                                    buttons: ["Salir", "Desea Cambiar los datos?"],
                                    warningMode: true
                                }).then((opt) => {
                                    if (opt) {
                                        this.abrirEditar(respuesta.id);
                                    } else {

                                    };
                                });
                                this.cuenta=this.iniCuenta();
                                break;
                            case "OK":
                                this.cuenta.nom_cuenta=respuesta.data.nombres;
                                this.cuenta.ape_cuenta=respuesta.data.apellidoPaterno+" "+respuesta.data.apellidoMaterno;
                                break;
                            default:
                                break;
                        }
                        // this.table = response.data;
                    });
                    return true;
                }
            }
            return false;
        },
        searching(){
            this.listar();
            return true
        }
    },
    mounted() {
        this.listar();
        this.listarFundo();
    },
    methods: {
        iniCuenta(){
            this.errors={};
            return {
                nombre: null,
                apellido:null,
                usuario:null,
                password: null,
                rol: 'COMUN',
                fundo_id: null
            }
        },
        listarFundo(){
            axios.get(url_base+'/fundo?all=true')
            .then(response => {
                this.fundos = response.data;
            });
        },
        listar(n=this.table.from){
            axios.get(url_base+'/cuenta?page='+n+'&search='+this.search)
            .then(response => {
                this.table = response.data;
            })
        },
        grabarNuevo(){
            axios.post(url_base+'/cuenta',this.cuenta)
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case "VALIDATION":
                        this.errors=respuesta.data;
                        break;
                    case "OK":
                        this.listar();
                        this.cuenta=this.iniCuenta();
                        swal("", "cuenta Registrado", "success");
                        break;
                    default:
                        break;
                }
            });
            
        },
        actualizarEstado(id){
            axios.post(url_base+'/cuenta/'+id+'/estado')
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
            axios.post(url_base+'/cuenta/'+this.cuenta_editar.id+'?_method=PUT',this.cuenta_editar)
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case "VALIDATION":
                        this.errors_editar=respuesta.data;
                        break;
                    case "OK":
                        this.cuenta_editar=this.iniCuenta();
                        this.listar();
                        swal("", "cuenta Actualizado", "success");
                        $('#modal-editar').modal('hide');
                        break;
                    default:
                        break;
                }
            });
        },
        abrirEditar(id){
            axios.get(url_base+'/cuenta/'+id)
            .then(response => {
                this.cuenta_editar = response.data;
            })
            $('#modal-editar').modal();
        },
        abrirPrivilegios(id){
            axios.get(url_base+'/privilegios?cuenta_id='+id)
            .then(response => {
                this.cuenta_privilegios.privilegios = response.data;
            })
            this.cuenta_privilegios.cuenta_id=id;
            $('#modal-privilegios').modal();
        },
        guardarPrivilegios(){
            axios.post(url_base+'/privilegios',this.cuenta_privilegios)
            .then(response => {
                var respuesta=response.data;
                if (respuesta.status=="OK") {
                    swal("", respuesta.data, "success");
                }else{
                    swal("", respuesta.data, "warning");
                }
            });
        }
    },
}
</script>