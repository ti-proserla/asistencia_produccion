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
                            <Input title="DNI:" v-model="operador.dni"></Input>
                            <Input title="Nombre:" v-model="operador.nom_operador"></Input>
                            <Input title="Apellido:" v-model="operador.ape_operador"></Input>
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
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>DNI</th>
                                    <th>Nombre</th>
                                    <th>Apellido</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="operador in operadores">
                                    <td>{{operador.dni}}</td>
                                    <td>{{operador.nom_operador}}</td>
                                    <td>{{operador.ape_operador}}</td>
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
import Input from '../dragon-desing/dg-input.vue'

export default {
    components:{
        Input, 
    },
    data() {
        return {
            operador: this.iniOperador(),
            operadores: []
        }
    },
    mounted() {
        this.listar();
    },
    methods: {
        listar(){
            axios.get(url_base+'/operador')
            .then(response => {
                this.operadores = response.data;
            })
        },
        iniOperador(){
            return {
                dni: null,
                nom_operador: null,
                ape_operador:null
            }
        },
        grabarNuevo(){
            axios.post(url_base+'/operador',this.operador)
            .then(response => {
                this.operador=this.iniOperador();
                this.listar();
            });
        }
    },
}
</script>