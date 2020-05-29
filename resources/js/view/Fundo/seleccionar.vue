<template>
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Seleccionar fundo a Usar</h4>
        </div>
        <div class="card-body text-center">
            <select class="form-control mb-3" v-model="fundo_id">
                <option value="">- Seleccionar Fundo -</option>
                <option v-for="fundo in fundos" :value="fundo">{{ fundo }}</option>
            </select>
            <button @click="seleccionar()" class="btn btn-danger">Usar</button>
        </div>
    </div>
</template>
<script>
import { mapState,mapMutations } from 'vuex'

export default {
    data() {
        return {
            fundos: [],
            fundo_id: localStorage.getItem('fundo') || null
        }
    },
    mounted() {
        axios.get('../api/privilegios').then(res=>{
            this.fundos=res.data;
        });
    },
    methods: {
        seleccionar(){
            this.$store.commit( 'updateFundo' , this.fundo_id );
        }
    },
}
</script>