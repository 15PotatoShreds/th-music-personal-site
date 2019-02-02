<template>
    <div class="album-container">
        <div class="album-info-container">
            <div class="album-cover-container">
                <img :src="cover" alt="" class="album-cover"/>
            </div>

            <div class="album-info-text-container">
                <el-input
                    v-model="albumID"
                    placeholder="该专辑在THBWiki的SMWID"
                    :disabled="notEditable"
                    style="display: block">
                </el-input>
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
            </div>

            <div class="album-info-button-container">
                <el-button
                    type="success"
                    @click="confirmAdd"
                    :disabled="notEditable"
                    class="album-info-button"
                    :loading="adding"
                    plain>
                    {{confirmButtonText}}
                </el-button>
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
                        class="album-info-button"
                        @click="searchAlbumByTHBWiki"
                        :disabled="notEditable"
                        plain>
                        THBWiki获取信息
                    </el-button>
                </el-popover>
                <el-button
                    type="danger"
                    class="album-info-button"
                    @click="clearAll"
                    :disabled="adding"
                    plain>
                    清空列表
                </el-button>
            </div>
        </div>

        <div class="album-music-container">
            <el-table
                :data="musicList"
                style="width: 100%"
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
                    width="250"
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
                <el-table-column
                    label="演唱"
                    width="300">
                    <template slot-scope="scope">
                        <el-select
                            v-model="scope.row.vocal"
                            multiple
                            filterable
                            allow-create
                            default-first-option
                            placeholder="演唱"
                            :disabled="notEditable"
                            style="display: block"
                        >
                            <el-option
                                v-for="item in optionsVocal"
                                :key="item.value"
                                :label="item.label"
                                :value="item.value">
                            </el-option>
                        </el-select>
                    </template>
                </el-table-column>
                <el-table-column
                    label="原曲"
                    width="400">
                    <template slot-scope="scope">
                        <el-select
                            v-model="scope.row.originMusic"
                            multiple
                            filterable
                            allow-create
                            default-first-option
                            :disabled="notEditable"
                            placeholder="原曲"
                            style="display: block"
                        >
                            <el-option
                                v-for="item in optionsOrigin"
                                :key="item.value"
                                :label="item.label"
                                :value="item.value">
                            </el-option>
                        </el-select>
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
    </div>
</template>

<script>
    import axios from "axios";

    export default {
        name: "AddAlbum",
        props: {
            index: {
                type: Number,
                required: true
            }
        },
        methods: {
            addRow() {
                let newDiscNo = 1;
                let newTrackNo = 1;
                if(this.musicList.length > 0) {
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
                        f: 'alname+circle+coverurl',
                        p: 'discno+trackno+name+artist+ogmusic',
                        a: id
                    }
                }).then(response => {
                    this.musicList = [];
                    this.albumName = response.data[0][0][1];
                    this.producer = response.data[0][1][1].split("/");
                    this.cover = response.data[0][2][1];
                    response.data[1].forEach(v=>{
                        this.musicList.push({
                            musicID: v[0][1],
                            discNo: v[1][1],
                            trackNo: v[2][1],
                            title: v[3][1],
                            vocal: v[4][1].split("/"),
                            originMusic: v[5][1].split("/")
                        });
                    });
                });
            },
            confirmAdd() {
                this.confirmButtonText = '正在添加';
                this.adding = true;
                this.notEditable = true;
                let musicList = [];
                this.musicList.forEach(v=>{
                    musicList.push({
                        musicID: v['musicID'],
                        discNo: v['discNo'],
                        trackNo: v['trackNo'],
                        title: v['title']
                    });
                });
                axios.post('/apis/THMusic/admin/addAlbum.php', {
                    albumID: this.albumID,
                    albumName: this.albumName,
                    cover: this.cover,
                    producer: this.producer,
                    musicList: musicList
                }).then(() => {
                    this.adding = false;
                    this.$message({
                        message: '添加成功！',
                        type: 'success'
                    });
                    this.confirmButtonText = '添加成功';
                }).catch(error => {
                    this.adding = false;
                    this.$message({
                        message: '添加失败！' + error,
                        type: 'success'
                    });
                    this.confirmButtonText = '添加失败，重试';
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
                this.cover = '';
                this.producer = [];
                this.musicList = [];
            }
        },
        data() {
            return {
                albumID: '',
                albumName: '',
                cover: '',
                producer: [],
                musicList: [],
                optionsProducer: [],
                optionsVocal: [],
                optionsOrigin: [],
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
    .album-container {
        margin: 10px 0;
        max-width: 1590px;
    }

    .album-info-container {
        padding-top: 5px;
        padding-bottom: 5px;
    }

    .album-info-button-container {
        display: inline-block;
        vertical-align: top;
        padding-left: 5px;
    }

    .album-info-button {
        display: block;
        width: 150px;
    }

    .album-cover-container {
        display: inline-block;
        left: 10px;
        padding: 5px;
    }

    .album-cover {
        width: 150px;
    }

    .album-info-text-container {
        display: inline-block;
        vertical-align: top;
        width: calc(100% - 330px);
        min-width: 300px;
        max-width: 500px;
    }
</style>
