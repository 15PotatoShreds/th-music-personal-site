<template>
    <el-container>
        <el-header style="height: 50px; padding: 5px 0 0 5px;">
            <router-link to="/">
                <el-button
                        type="primary"
                        plain
                        icon="el-icon-arrow-left"
                        :disabled="adding"
                        class="button"
                >
                    返回主页面
                </el-button>
            </router-link>

            <el-popover
                    placement="right"
                    width="500"
                    trigger="click"
                    v-model="popoverState"
            >
                <el-table :data="albumSearchResultList" max-height="250" v-loading="albumListLoading">
                    <el-table-column width="200" property="albumName" label="专辑名称"></el-table-column>
                    <el-table-column width="200" property="producer" label="制作方"></el-table-column>
                    <el-table-column width="80">
                        <template slot-scope="scope">
                            <el-button
                                    size="mini"
                                    type="success"
                                    icon="el-icon-check"
                                    @click="autoFillByTHBWiki(scope.row.id)"
                            >
                            </el-button>
                        </template>
                    </el-table-column>
                </el-table>
                <el-button
                        type="primary"
                        slot="reference"
                        @click="searchAlbumByTHBWiki"
                        :disabled="notEditable"
                        icon="el-icon-search"
                        class="button"
                        plain
                >
                    从THBWiki获取
                </el-button>
            </el-popover>

            <el-button
                    type="success"
                    @click="confirmAdd"
                    :disabled="notEditable"
                    :loading="adding"
                    plain
                    icon="el-icon-check"
                    class="button"
            >
                {{confirmButtonText}}
            </el-button>

            <el-button
                    type="danger"
                    @click="clearAll"
                    :disabled="adding"
                    icon="el-icon-delete"
                    plain
                    class="button"
            >
                清空列表
            </el-button>
        </el-header>
        <el-main style="padding: 0 0 0 10px">
            <div class="album-info-container">
                <div class="album-info-text-container">
                    <el-input
                            v-model="albumName"
                            placeholder="专辑名称"
                            :disabled="notEditable"
                            style="display: block">
                    </el-input>
                    <el-select
                            v-model="producer"
                            multiple
                            filterable
                            allow-create
                            default-first-option
                            placeholder="制作方"
                            :disabled="notEditable"
                            style="display: block"
                    >
                        <el-option
                                v-for="item in optionsProducer"
                                :key="item.value"
                                :label="item.label"
                                :value="item.value"
                        >
                        </el-option>
                    </el-select>
                    <el-input
                            v-model="albumID"
                            placeholder="该专辑在THBWiki的SMWID"
                            :disabled="notEditable"
                            style="display: block">
                    </el-input>
                </div>

                <div class="album-info-button-container">



                </div>
            </div>

            <div class="album-music-container">
                <el-table
                        :data="musicList"
                        style="width: 700px"
                >
                    <el-table-column
                            label="SMWID"
                            width="120">
                        <template slot-scope="scope">
                            <el-input
                                    v-model="scope.row.musicID"
                                    :disabled="notEditable"
                                    placeholder="SMWID">
                            </el-input>
                        </template>
                    </el-table-column>
                    <el-table-column
                            label="碟号"
                            width="80">
                        <template slot-scope="scope">
                            <el-input
                                    v-model="scope.row.discNo"
                                    :disabled="notEditable"
                                    placeholder="碟号">
                            </el-input>
                        </template>
                    </el-table-column>
                    <el-table-column
                            label="音轨"
                            width="80">
                        <template slot-scope="scope">
                            <el-input
                                    v-model="scope.row.trackNo"
                                    :disabled="notEditable"
                                    placeholder="音轨">
                            </el-input>
                        </template>
                    </el-table-column>
                    <el-table-column
                            label="标题"
                            width="300"
                    >
                        <template slot-scope="scope">
                            <el-input
                                    type="textarea"
                                    autosize
                                    resize="none"
                                    placeholder="标题"
                                    :disabled="notEditable"
                                    v-model="scope.row.title">
                            </el-input>
                        </template>
                    </el-table-column>
                    <el-table-column align="right">
                        <template slot="header" slot-scope="scope">
                            <el-button
                                    type="text"
                                    :disabled="notEditable"
                                    @click="addRow">
                                添加一行
                            </el-button>
                        </template>
                        <template slot-scope="scope">
                            <el-button
                                    type="text"
                                    size="small"
                                    :disabled="notEditable"
                                    @click.native.prevent="deleteRow(scope.$index, musicList)">
                                删除
                            </el-button>
                        </template>
                    </el-table-column>
                </el-table>
            </div>
        </el-main>
    </el-container>

</template>

<script>
    import axios from "axios";

    export default {
        name: "AddAlbum",
        methods: {
            addRow() {
                let newDiscNo = 1;
                let newTrackNo = 1;
                if (this.musicList.length > 0) {
                    newDiscNo = this.musicList[this.musicList.length - 1]['discNo'];
                    newTrackNo = parseInt(this.musicList[this.musicList.length - 1]['trackNo']) + 1;
                }
                this.musicList.push({
                    discNo: newDiscNo,
                    trackNo: newTrackNo,
                    title: '',
                    vocal: [],
                    originMusic: []
                });
            },
            deleteRow(index, rows) {
                rows.splice(index, 1);
            },
            searchAlbumByTHBWiki() {
                this.albumSearchResultList = [];
                if (this.albumName === '') {
                    return
                }
                this.albumListLoading = true;
                axios.get('apis/THBWiki/album.php', {
                    params: {
                        m: 'sa',
                        g: 2,
                        o: 1,
                        d: 'nm',
                        l: 10,
                        v: this.albumName
                    }
                }).then(response => {
                    if (response.data.length === 0) {
                        this.albumListLoading = false;
                        return
                    }
                    response.data.forEach(e => {
                        this.albumSearchResultList.push({
                            id: e[0],
                            albumName: e[1],
                            producer: e[2]
                        });
                        this.albumListLoading = false;
                    });
                });
            },
            autoFillByTHBWiki: function (id) {
                this.popoverState = false;
                this.albumID = id;
                axios.get('apis/THBWiki/album.php', {
                    params: {
                        m: 'ga',
                        g: 2,
                        o: 1,
                        s: '/',
                        d: 'nm',
                        f: 'alname+circle',
                        p: 'discno+trackno+name',
                        a: id
                    }
                }).then(response => {
                    this.musicList = [];
                    this.albumName = response.data[0][0][1];
                    this.producer = response.data[0][1][1].split("/");
                    response.data[1].forEach(v => {
                        this.musicList.push({
                            musicID: v[0][1],
                            discNo: v[1][1],
                            trackNo: v[2][1],
                            title: v[3][1]
                        });
                    });
                });
            },
            confirmAdd() {
                this.confirmButtonText = '正在添加';
                this.adding = true;
                this.notEditable = true;
                axios.post('/apis/THMusic/admin/addAlbum.php', {
                    albumID: this.albumID,
                    albumName: this.albumName,
                    producer: this.producer,
                    musicList: this.musicList
                }).then(response => {
                    if (response.data === 'success') {
                        this.adding = false;
                        this.$message({
                            message: '添加成功！',
                            type: 'success'
                        });
                        this.confirmButtonText = '添加成功';
                    } else if (response.data === 'Already Exist') {
                        this.adding = false;
                        this.$message({
                            showClose: true,
                            duration: 15000,
                            message: '该专辑已存在！',
                            type: 'error'
                        });
                        this.confirmButtonText = '确认添加';
                        this.notEditable = false;
                    }
                }).catch(error => {
                    this.adding = false;
                    this.$message({
                        showClose: true,
                        duration: 15000,
                        message: '添加失败！' + error,
                        type: 'error'
                    });
                    this.confirmButtonText = '确认添加';
                    this.notEditable = false;
                    console.log(error);
                });
            },
            clearAll() {
                this.confirmButtonText = '确认添加';
                this.adding = false;
                this.notEditable = false;
                this.albumID = '';
                this.albumName = '';
                this.producer = [];
                this.musicList = [];
            }
        },
        data() {
            return {
                albumID: '',
                albumName: '',
                producer: [],
                musicList: [],
                optionsProducer: [],
                albumSearchResultList: [],
                albumListLoading: false,
                popoverState: false,
                confirmButtonText: '确认添加',
                adding: false,
                notEditable: false
            }
        }
    }
</script>

<style scoped>
    .button {
        width: 150px;
        margin: 0 10px 0 0;
    }
    .album-info-container {
        padding-top: 5px;
        padding-bottom: 5px;
    }

    .album-info-text-container {
        display: inline-block;
        vertical-align: top;
        width: 100%;
        min-width: 300px;
        max-width: 700px;
    }
</style>
