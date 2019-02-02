<template>
    <el-container style="height: 100vh; border: 1px solid #eee">
        <el-aside width="140px" style="background-color: rgb(238, 241, 246)">
            <el-button type="primary" icon="el-icon-plus" @click="">添加专辑</el-button>
        </el-aside>
        <el-main style="padding: 0 20px 0 20px">
            <AddAlbum :index=0></AddAlbum>
        </el-main>
    </el-container>
</template>

<script>
    import AddAlbum from "./components/AddAlbum";

    export default {
        name: 'app',
        components: {
            AddAlbum,
        },
        methods: {
        },
        data() {
            return {
            }
        }
    }
</script>

<style>
</style>
