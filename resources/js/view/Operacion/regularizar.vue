<template>
    <div>
        <v-select :options="['Canada', 'United States']" @search="getOperadores()"></v-select>
    </div>
</template>
<script>
export default {
    methods: {
        getOperadores(search,loading){
            loading(true);
            
        }
    },
}
</script>