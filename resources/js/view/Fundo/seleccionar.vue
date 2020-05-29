<template>
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Seleccionar fundo a Usar</h4>
        </div>
        <div class="card-body">
            <select name="" id="">
                <option v-for="fundo in fundos" value="">{{ fundo }}</option>
            </select>
            <button @click="">Usar</button>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            fundos: []
        }
    },
    mounted() {
        axios.get('../api/privilegios').then(res=>{
            this.fundos=res.data;
        });
    },
    methods: {
        seleccionar(){
            
        }
    },
}
</script>