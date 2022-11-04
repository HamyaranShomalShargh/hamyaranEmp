<template>
    <div class="loading_window">
        <div class="bg-menu loading_box d-flex flex-row justify-content-start align-items-center p-4 rounded">
            <i class="fa fa-cog fa-spin fa-1-6x ml-2"></i>
            <span class="iransans" style="font-size: 1.2rem">لطفا منتظر بمانید...</span>
        </div>
    </div>
</template>

<script>
export default {
    name: "Loading",
    props: ["show"]
}
</script>

<style scoped>
.loading_window{
    position: absolute;
    width: 100%;
    height: 100%;
    overflow: hidden;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    background: rgba(0, 0, 0, 0.5);
    z-index: 10000;
    top:0;
    right:0
}
.loading_box{
    width: calc(100vw / 6);
}
@media screen and (max-width: 768px){
    .loading_box{
        width: 90vw;
    }
}
</style>
