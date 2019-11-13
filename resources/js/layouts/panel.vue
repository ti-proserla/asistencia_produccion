<template>
    <div id="app">
        <div class="sidebar">
            <ul>
                <li class="">
                    <a href="#">
                        <i class="material-icons">person</i>
                        <p>Operadores</p>
                    </a>
                </li>
            </ul>            
        </div>
        <div class="content">
            <slot/>
        </div>
    </div>
</template>
<script>
export default {
    
}
</script>