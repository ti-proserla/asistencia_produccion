<template>
<div>
    <div class="row">
        <div class="col-sm-4">
            <v-select :options="opt" @search="getOperadores" :filterable="false" v-model="hola">
                <template slot="no-options">
                    Buscar Operadores..
                </template>
                <template slot="option" slot-scope="option">
                    <div class="v-select-options d-center">
                        <img :src='url(option.foto)'/> 
                        {{ option.nom_operador }}
                    </div>
                </template>
                <template slot="selected-option" slot-scope="option">
                    <div class="selected d-center v-select-options">
                        <img :src='url(option.foto)'/> 
                        {{ option.nom_operador }}
                    </div>
                </template>
            </v-select>
        </div>
        <div class="col-sm-2">
            <Select title="Turno:" v-model="turno_id">
                <option v-for="turno in turnos" :value="turno.id">{{ turno.descripcion }}</option>
            </Select>
        </div>
        <div class="col-sm-4">
            <button class="btn btn-danger" @click="buscar">Buscar</button>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-8">
            <table class="table">
                <thead>
                    <tr>
                        <th>N° Par</th>
                        <th>Ingreso</th>
                        <th>Salida</th>
                        <th>Editar</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(marca,index) in marcas">
                        <td>{{index+1}}</td>
                        <td v-if="marca_edit_index!=index">{{marca.ingreso}}</td>
                        <td v-else><input type="datetime-local" v-model="marca_edit.ingreso"></td>
                        <td v-if="marca_edit_index!=index">{{marca.salida}}</td>
                        <td v-else><input type="datetime-local" v-model="marca_edit.salida"></td>
                        <td v-if="marca_edit_index!=index"><button class="btn-link-warning" @click="selectEdit(index)">E</button></td>
                        <td v-else>
                            <button class="btn-link-info" @click="editar()">G</button>
                            <button class="btn-link-danger" @click="cancelar()">X</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
</template>
<script>
import Select from '../../dragon-desing/dg-select.vue'

export default {
    components:{
        Select
    },
    data() {
        return {
            hol:null,
            opt:[],
            hola:null,
            turnos:[],
            turno_id: null,
            marcas:[],
            marca_edit: null,
            marca_edit_index:-1
            
        }
    },
    mounted() {
        this.listarTurnos();
    },
    computed: {
        
    },
    methods: {
        url(data){
            return url_base+'/../storage/operador/'+data; 
        },
        getOperadores(search,loading){
            setTimeout(() => {
                if (search.length>0) {
                    loading(true);
                    axios.get(url_base+'/operador?all=true&search='+search)
                    .then(response => {
                        var respuesta=response.data;
                        this.opt=respuesta;
                        loading(false);
                    })
                }else{
                    this.opt=[];
                }
            }, 300);
        },
        selectEdit(index){
            this.marca_edit_index=index;
            this.marca_edit=JSON.parse(JSON.stringify(this.marcas[index]));
            this.marca_edit.ingreso=moment(this.marca_edit.ingreso,'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DDTHH:mm:ss');
            this.marca_edit.salida=moment(this.marca_edit.salida,'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DDTHH:mm:ss');
        },
        editar(){
            axios.post(url_base+'/marcador/'+this.marca_edit.id+'?_method=PUT',this.marca_edit)
            .then(response => {
                var respuesta=response.data;
                    switch (respuesta.status) {
                        case "VALIDATION":
                            this.errors_editar=respuesta.data;
                            break;
                        case "OK":
                            swal("", "Marca Actualizada", "success");
                            this.cancelar();
                            this.buscar();
                            break;
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
            axios.get(url_base+'/marcador?operador_id='+this.hola.id+'&turno_id='+this.turno_id)
            .then(response => {
                this.cancelar();
                this.marcas=response.data;
            });
        },
        cancelar(){
            this.marca_edit_index=-1;
            this.marca_edit=null;
        }
    },
}
</script>