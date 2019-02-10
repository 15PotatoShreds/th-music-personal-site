<template>
    <el-container>
        <el-header style="height: 50px; padding: 5px 0 0 5px;">
            <router-link to="/AddAlbum">
                <el-button
                        type="primary"
                        plain
                        icon="el-icon-plus"
                        class="button"
                >
                    添加专辑
                </el-button>
            </router-link>
            <el-select
                    v-model="producer"
                    multiple
                    filterable
                    allow-create
                    default-first-option
                    placeholder="制作方"
                    style="width: 300px;"
            >
                <el-option
                        v-for="item in optionsProducer"
                        :key="item.value"
                        :label="item.label"
                        :value="item.value"
                >
                </el-option>
            </el-select>
            <el-checkbox v-model="noFileCheck">
                未上传文件
            </el-checkbox>
            <el-button
                    type="success"
                    plain
                    icon="el-icon-tickets"
                    class="button"
                    @click="query"
            >
                检索
            </el-button>
        </el-header>
        <el-main>
            <el-collapse v-for="album in list" :key="album.AlbumName" style="width: 1000px;">
                <el-collapse-item>
                    <template slot="title">
                        <span class="collapse-title-span">
                            <strong>专辑名称：</strong>
                            <span>{{album.AlbumName}}</span>
                        </span>
                        <span class="collapse-title-span">
                            <strong>制作方：</strong>
                            <span>{{album.Producer.join("、")}}</span>
                        </span>
                        <span class="collapse-title-span">
                            <strong>SMWID：</strong>
                            <span>{{album.AlbumID}}</span>
                        </span>
                        <el-button size="mini" @click="editAlbum">
                            编辑
                        </el-button>
                    </template>

                    <el-table
                            :data="album.MusicList"
                            style="width: 100%">
                        <el-table-column
                                prop="MusicID"
                                label="SMWID"
                                width="100">
                        </el-table-column>
                        <el-table-column
                                prop="DiscNo"
                                label="碟号"
                                width="60">
                        </el-table-column>
                        <el-table-column
                                prop="TrackNo"
                                label="音轨"
                                width="60">
                        </el-table-column>
                        <el-table-column
                                prop="Title"
                                label="标题"
                                width="300">
                        </el-table-column>
                        <el-table-column
                                label="文件"
                                width="300">
                            <template slot-scope="scope">
                                <template v-if="scope.row.File === ''">
                                    <el-upload
                                            action="/apis/THMusic/admin/uploadFile.php"
                                            :limit="1"
                                            :data="{MusicID: scope.row.MusicID, AlbumName: album.AlbumName, AlbumID: album.AlbumID}"
                                    >
                                        <el-button size="small" type="text">上传文件</el-button>
                                    </el-upload>
                                </template>
                                <template v-else>
                                    {{scope.row.File}}
                                </template>
                            </template>
                        </el-table-column>
                        <el-table-column label="操作" width="100">
                            <template slot-scope="scope">
                                <el-button size="mini">
                                    编辑
                                </el-button>
                            </template>
                        </el-table-column>
                    </el-table>
                </el-collapse-item>
            </el-collapse>
        </el-main>
    </el-container>
</template>

<script>
    import axios from "axios";

    export default {
        name: "List",
        methods: {
            query() {
                axios.get('/apis/THMusic/admin/getAlbumList.php', {
                    params: {}
                }).then(response => {
                    this.list = response.data;
                });
            },
            editAlbum() {
                console.log("1");
            }
        },
        data() {
            return {
                producer: [],
                optionsProducer: [],
                noFileCheck: false,
                list: []
            }
        }
    }
</script>

<style scoped>
    .button {
        width: 150px;
        margin: 0 10px 0 0;
    }

    .collapse-title-span {
        padding: 0 10px 0 0;
    }
</style>