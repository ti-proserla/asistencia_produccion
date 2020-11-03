<template>
    <div>
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Nuevo Operador </h4>
                    </div>
                    <div class="card-body">
                        <form action="" v-on:submit.prevent="grabarNuevo()">
                            <label for="" hidden>{{consulta}}</label>
                            <Input title="DNI:" v-model="operador.dni" :error="errors.dni" ></Input>
                            <Input title="Nombre:" v-model="operador.nom_operador" :error="errors.nom_operador"></Input>
                            <Input title="Apellido:" v-model="operador.ape_operador" :error="errors.ape_operador"></Input>
                            <Select title="Planilla:" v-model="operador.planilla_id" :error="errors.planilla_id">
                                <option value=""></option>
                                <option v-for="planilla in planillas" :value="planilla.id">{{ planilla.nom_planilla }}</option>
                            </Select>
                            <Select title="Cargo:" v-model="operador.cargo_id" :error="errors.cargo_id">
                                <option value="">Sin Cargo</option>
                                <option v-for="cargo in cargos" :value="cargo.id">{{ cargo.nom_cargo }}</option>
                            </Select>
                            <div class="croppa-center">
                                <croppa 
                                    @new-image-drawn="girarIfxe()" 
                                    v-model="myCroppa" 
                                    :width="croppaConfig.horizontal" 
                                    :height="croppaConfig.vertical" 
                                    :quality="croppaConfig.quality" :prevent-white-space="true"
                                ></croppa>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-success mt-3">
                                    Guardar
                                </button>
                                <button @click="grabarNuevo('imprimir');" type="button" class="btn btn-success mt-3">
                                    Guardar e Imprimir Código
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Lista de Operadores</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-2">
                                <label for="" class="my-3">Buscar: </label>
                            </div>
                            <div class="col-8">
                                <input @keyup="listar(1)" class="form-control" v-model="search"></input>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>DNI</th>
                                        <th>Nombres y Apellidos</th>
                                        <th>Editar</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="operador in table.data">
                                        <td>{{operador.dni}}</td>
                                        <td>{{operador.nom_operador}} {{operador.ape_operador}}</td>
                                        <td>
                                            <button @click="abrirEditar(operador.id)" class="btn-link-info">
                                                <i class="material-icons">create</i>
                                            </button>
                                        </td>
                                        <td>
                                            <button v-if="operador.estado=='0'" @click="actualizarEstado(operador.id)" class="btn-link-info">
                                                <i class="material-icons">radio_button_checked</i>
                                            </button>
                                            <button v-else @click="actualizarEstado(operador.id)" class="btn-link-gray">
                                                <i class="material-icons">radio_button_unchecked</i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="pagination">
                            <div class="row">
                                <div class="col-9 text-left">
                                    <h6>Pagina {{ selectPage }} de {{ table.last_page}} (TOTAL: {{table.total}})</h6>
                                </div>
                                <div class="col-3">
                                    <button v-if="selectPage!=1" @click="listar(Number(selectPage)-1)"><</button>
                                    <select v-model="selectPage"  v-on:change="listar()">
                                        <option v-for="n in table.last_page">{{n}}</option>
                                    </select>
                                    <a @click="listar(Number(selectPage)+1)" v-if="!(selectPage==table.last_page||table.last_page==1)">></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--Modal Editar-->
        <div id="modal-editar" class="modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Operador</h5>
                        <button type="button" class="close" @click="cancelarActualizar()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" v-on:submit.prevent="grabarEditar()">
                            <div class="row form-group">
                                <div class="col-10">
                                    <label for="">DNI: </label> {{ operador_editar.dni }}
                                </div>
                                <div class="col-2">
                                    <a class="btn btn-sm btn-success" @click="consultaJNE()">JNE</a>
                                </div>
                            </div>
                            <Input title="Nombre:" v-model="operador_editar.nom_operador" :error="errors_editar.nom_operador"></Input>
                            <Input title="Apellido:" v-model="operador_editar.ape_operador" :error="errors_editar.ape_operador"></Input>
                            <Select title="Planilla:" v-model="operador_editar.planilla_id" :error="errors_editar.planilla_id">
                                <option value=""></option>
                                <option v-for="planilla in planillas" :value="planilla.id">{{ planilla.nom_planilla }}</option>
                            </Select>
                            <Select title="Cargo:" v-model="operador_editar.cargo_id" :error="errors_editar.cargo_id">
                                <option value=""></option>
                                <option v-for="cargo in cargos" :value="cargo.id">{{ cargo.nom_cargo }}</option>
                            </Select>
                            <div class="croppa-center my-3">
                                
                                <croppa @new-image-drawn="girarIfxe2()" placeholder="Seleccionar imagen" data-exif-orientation="6" :initial-image="url_imagen"  v-model="myCroppa2" :width="croppaConfig.horizontal" :height="croppaConfig.vertical" :quality="croppaConfig.quality" :prevent-white-space="true">
                                </croppa>
                                <!-- <div class="mt-1 mb-3">
                                    <button @click="prueba()" type="button" class="btn btn-info btn-sm">90°</button>
                                    <button @click="myCroppa2.rotate(-1)" type="button" class="btn btn-info btn-sm">-90°</button>
                                </div> -->
                            </div>
                            <div class="text-center">
                                <button @click="cancelarActualizar()" type="button" class="btn btn-danger">Cancelar</button>
                                <button type="submit" class="btn btn-success">Guardar</button>
                                <button type="button" class="btn btn-info" @click="abrirCodeBar(operador_editar.dni,operador_editar.nom_operador+' '+operador_editar.ape_operador)">Imprimir Código</button>
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
                        <h5 class="modal-title">FotoCheck</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" style="text-align: center;">
                        <iframe :src="url" frameborder="0" width="200" height="300"></iframe>
                    </div>
                </div>
            </div>
        </div>
        <!--Modal codebar-->
        <div id="modal-codebar" class="modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <iframe :src="url_codebar" frameborder="0" width="100%" height="500px"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import VueBarcode from 'vue-barcode';
import Input from '../../dragon-desing/dg-input.vue'
import Select from '../../dragon-desing/dg-select.vue'

export default {
    components:{
        Input,
        Select,
        'barcode': VueBarcode 
    },
    data() {
        return {
            search: null,
            operador: this.iniOperador(), //datos de logeo
            operador_editar: this.iniOperador(),
            planillas:  [],
            cargos:     [],
            errors:     {}, //datos de errores
            errors_editar: {}, //datos de errores
            //Datos de Tabla:
            table:{
                data:[]
            },
            selectPage: 1,
            /**
             * 
             */
            url: null,
            myCroppa: {}, 
            myCroppa2: {}, 
            croppaConfig: {
                horizontal:  225,
                vertical:  300 ,
                quality: 2
            },
            url_codebar: ''
        }
    },
    computed: {
        url_imagen(foto){
            return url_base+'/../storage/operador/'+this.operador_editar.foto;
        },
        consulta(){
            if (this.operador.dni!=null) {
                if (this.operador.dni.length==8) {
                    axios.get(url_base+'/jne/dni/'+this.operador.dni)
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
                                this.operador=this.iniOperador();
                                break;
                            case "OK":
                                this.operador.nom_operador=respuesta.data.nombres;
                                this.operador.ape_operador=respuesta.data.apellidoPaterno+" "+respuesta.data.apellidoMaterno;
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
        this.listarPlanilla();
        this.listarCargo();
    },
    methods: {
        abrirCodeBar(dni,nom_ape){
            console.log(nom_ape);
            this.url_codebar='/code-frame/'+dni+'/'+nom_ape;
            $('#modal-codebar').modal();
        },
        consultaJNE(){
            if (this.operador_editar.dni!=null) {
                if (this.operador_editar.dni.length==8) {
                    axios.get(url_base+'/jne/dni/'+this.operador_editar.dni+'?all=true')
                    .then(response => {
                        var respuesta=response.data;
                        switch (respuesta.status) {
                            case "OK":
                                this.operador_editar.nom_operador=respuesta.data.nombres;
                                this.operador_editar.ape_operador=respuesta.data.apellidoPaterno+" "+respuesta.data.apellidoMaterno;
                                break;
                            default:
                                break;
                        }
                        // this.table = response.data;
                    });
                    return true;
                }
            }
        },
        girarIfxe2(){            
            if (this.myCroppa2.getMetadata().orientation==6) {
                var dat=this.myCroppa2.getMetadata()
                dat.orientation=1;
                this.myCroppa2.applyMetadata(dat);      
            }
        },
        girarIfxe(){            
            if (this.myCroppa.getMetadata().orientation==6) {
                var dat=this.myCroppa.getMetadata()
                dat.orientation=1;
                this.myCroppa.applyMetadata(dat);      
            }
        },
        listar(n=this.selectPage){
            this.selectPage=n;
            axios.get(url_base+'/operador?page='+n+'&search='+this.search)
            .then(response => {
                this.table = response.data;
            })
        },
        listarPlanilla(n=this.table.from){
            axios.get(url_base+'/planilla?all=true')
            .then(response => {
                this.planillas = response.data;
                this.operador.planilla_id=this.planillas[0].id;
            })
        },
        listarCargo(){
            axios.get(url_base+'/cargo?all=true')
            .then(response => {
                this.cargos = response.data;
            })
        },
        iniOperador(){
            this.errors={};
            // var planilla_id= (!this.operador.planilla_id) ? this.operador.planilla_id: null;
            return {
                dni: null,
                nom_operador: null,
                ape_operador:null,
                planilla_id: null
            }
        },
        verFotoCheck(id){
            this.url=url_base+'/../fotocheck/'+id;
            $('#modal-fotocheck').modal();
        },
        grabarNuevo(imprimir){
            this.myCroppa.generateBlob((blob) => {
                var formData = new FormData()
                if (blob!=null) {
                    formData.append('foto', blob,'imagen.'+blob.type.split('/')[1]);
                }
                $.each(this.operador, function(key, value){
                    formData.append(key, value);
                })
                axios.post(url_base+'/operador',formData,{header : {'Content-Type' : 'multipart/form-data'}})
                .then(response => {
                    var respuesta=response.data;
                    switch (respuesta.status) {
                        case "VALIDATION":
                            this.errors=respuesta.data;
                            break;
                        case "OK":
                            if (imprimir=="imprimir") {
                                this.abrirCodeBar(this.operador.dni,this.operador.nom_operador+' '+this.operador.ape_operador);
                            }
                            this.operador=this.iniOperador();
                            swal("", "Operador Registrado", "success");
                            this.myCroppa.remove();
                            this.listar();
                            break;
                        default:
                            break;
                    }
                });
            }, 'image/jpeg');
            
        },
        actualizarEstado(id){
            axios.post(url_base+'/operador/'+id+'/estado')
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
            this.myCroppa2.generateBlob((blob) => {
                var formData = new FormData()
                if (blob!=null) {
                    formData.append('foto', blob,'imagen.'+blob.type.split('/')[1]);
                }
                $.each(this.operador_editar, function(key, value){
                    formData.append(key, value);
                })
            
                axios.post(url_base+'/operador/'+this.operador_editar.id+'?_method=PUT',formData,{header : {'Content-Type' : 'multipart/form-data'}})
                .then(response => {
                    var respuesta=response.data;
                    switch (respuesta.status) {
                        case "VALIDATION":
                            this.errors_editar=respuesta.data;
                            break;
                        case "OK":
                            this.operador_editar=this.iniOperador();
                            this.myCroppa2.remove();
                            this.listar(1);
                            swal("", "Operador Actualizado", "success");
                            $('#modal-editar').modal('hide');
                            break;
                        default:
                            break;
                    }
                });
            }, 'image/jpeg');

        },
        abrirEditar(id){
            axios.get(url_base+'/operador/'+id)
            .then(response => {
                this.operador_editar = response.data;
                this.myCroppa2.refresh();
            })
            $('#modal-editar').modal();
        },
        cancelarActualizar(){
            this.operador_editar=this.iniOperador();
            $('#modal-editar').modal('hide');
            this.myCroppa2.refresh();
        }
    },
}
</script>