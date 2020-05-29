<template>
<div>
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Regularizar Marcas</h4>
        </div>
        <div class="card-body">
            <div class="row">

                <div class="col-sm-8 col-lg-5 form-group">
                    <label for="">Seleccionar Trabajador:</label>
                    <v-select label="dni" :options="opt" @search="getOperadores" :value="hola" @input="setSelected" :filterable="false" >
                        <template slot="option" slot-scope="option">
                            <div class="v-select-options d-center">
                                {{ option.dni+' | '+option.nom_operador+" "+option.ape_operador }}
                            </div>
                        </template>
                        <template slot="selected-option" slot-scope="option">
                            <div class="selected d-center v-select-options">
                                {{ option.dni+' | '+option.nom_operador+" "+option.ape_operador }}
                            </div>
                        </template>
                        <template slot="no-options">
                            Filtrar datos
                        </template>
                    </v-select>
                </div>
                <div class="col-sm-4 col-lg-3">
                    <label for="">Fecha de Asitencia:</label>
                    <input type="date" class="form-control" v-model="fecha">
                </div>
                <div class="col-sm-4 col-lg-2">
                    <label for="">TURNO:</label>
                    <select v-model="turno" class="form-control">
                        <option value="1">Turno 1</option>
                        <option value="2">Turno 2</option>
                    </select>    
                </div>
                <div class="col-sm-4 col-lg-2">
                    <button class="btn btn-danger" @click="buscar">Buscar</button>
                </div>
            </div>
                    
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>N° Par</th>
                                <th>Ingreso</th>
                                <th>Salida</th>
                                <th>Editar</th>
                                <th>Eliminar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(marca,index) in marcas">
                                <td>{{index+1}}</td>
                                <td v-if="marca_edit_index!=index">{{marca.ingreso}}</td>
                                <td v-else><input type="datetime-local" v-model="marca_edit.ingreso"></td>
                                <td v-if="marca_edit_index!=index">{{marca.salida}}</td>
                                <td v-else><input type="datetime-local" v-model="marca_edit.salida"></td>
                                <td v-if="marca_edit_index!=index">
                                    <button class="btn-link-warning" @click="selectEdit(index)">
                                        <i class="material-icons">edit</i>
                                    </button>
                                </td>
                                <td v-else>
                                    <button class="btn-link-info" @click="editar()"><i class="material-icons">save</i></button>
                                    <button class="btn-link-danger" @click="cancelar()"><i class="material-icons">cancel</i></button>
                                </td>
                                <td v-if="marca_edit_index==index">
                                </td>
                                <td v-else>
                                    <button class="btn-link-danger">
                                        <i class="material-icons" @click="eliminar(marca.id)">
                                            delete
                                        </i>
                                    </button>

                                </td>
                            </tr>
                            <tr>
                                <td class="text-center" colspan="5">
                                    <button class="btn btn-sm btn-success" @click="agregar">Agregar Marca</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</template>
<script>
    import 'vue-search-select/dist/VueSearchSelect.css';
import Select from '../../dragon-desing/dg-select.vue';
import { ModelListSelect } from 'vue-search-select';

export default {
    components:{
        Select,
        ModelListSelect
    },
    data() {
        return {
            hol:null,
            opt:[],
            hola:null,
            turnos:[],
            turno_id: null,
            fecha: moment().format('YYYY-MM-DD'),
            marcas:[],
            marca_edit: null,
            marca_edit_index:-1,
            turno: 1,
        }
    },
    mounted() {
        this.listarTurnos();
    },
    computed: {
        
    },
    methods: {
        setSelected(value){
            this.hola=value  
        },
        url(data){
            return url_base+'/../storage/operador/'+data; 
        },
        getOperadores(search,loading){
            loading(true);
            this.search(loading, search, this);
        },
        search: _.debounce((loading, search, vm) => {
                vm.opt=[]
                if (search!="") {
                    axios.get(url_base+'/operador?all=true&search='+search)
                    .then(response => {
                        var respuesta=response.data;
                        vm.opt=respuesta;
                        loading(false);
                    })
                }else{
                    loading(false);
                }
            }, 350)
        ,
        selectEdit(index){
            this.marca_edit_index=index;
            this.marca_edit=JSON.parse(JSON.stringify(this.marcas[index]));
            this.marca_edit.ingreso=(this.marca_edit.ingreso!=null) ? moment(this.marca_edit.ingreso,'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DDTHH:mm:ss') : null;
            this.marca_edit.salida=(this.marca_edit.salida!=null) ? moment(this.marca_edit.salida,'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DDTHH:mm:ss'): null;
        },
        editar(){
            swal({
                title: "", 
                text: "¿Desea Actualizar Par de Marcas?", 
                icon: "info",
                buttons: true,
            }).then((value) => {
                if(value){
                    axios.post(url_base+'/marcador/'+this.marca_edit.id+'?_method=PUT',this.marca_edit)
                    .then(response => {
                        var respuesta=response.data;
                            switch (respuesta.status) {
                                case 'error':
                                    swal("",respuesta.data,"error");
                                    break;
                                case "VALIDATION":
                                    this.errors_editar=respuesta.data;
                                    break;
                                case "OK":
                                    swal("","Marca Actualizada","success")
                                    this.cancelar();
                                    this.buscar();
                                    break;
                            }
                    });
                }else{
                    this.buscar();
                }
            });
        },
        listarTurnos(){
            axios.get(url_base+'/turno?all=true')
            .then(response => {
                this.turnos = response.data;
                if (this.turnos.length>0) {
                    this.turno_id=this.turnos[0].id;
                }
            });
        },
        buscar(){
            axios.get(url_base+'/marcador?codigo_operador='+this.hola.dni+'&fecha='+this.fecha+'&turno='+this.turno)
            .then(response => {
                this.cancelar();
                this.marcas=response.data;
            });
        },
        agregar(){
            axios.get(url_base+'/marcador/add?codigo_operador='+this.hola.dni+'&fecha='+this.fecha+'&turno='+this.turno)
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case 'error':
                        swal("",respuesta.data,"error");
                        break;
                }
                this.buscar();
            });
        },
        eliminar(id){
            swal({
                title: "", 
                text: "¿Desea Eliminar Par de Marcas?", 
                icon: "warning",
                buttons: true,
            }).then((value) => {
                if(value){
                    axios.get(url_base+'/marcador/remove?codigo_operador='+this.hola.dni+'&marcador_id='+id)
                    .then(response => {
                        var respuesta=response.data;
                        switch (respuesta.status) {
                            case 'error':
                                swal("",respuesta.data,"error");
                                break;
                            case "OK":
                                swal("","Marca Eliminada","success")
                                this.buscar();
                                break;
                        }
                    });
                }else{
                    this.buscar();
                }
            });


        },
        cancelar(){
            this.marca_edit_index=-1;
            this.marca_edit=null;
        }
    },
}
</script>