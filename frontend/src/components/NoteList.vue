<template>
  <div>
    <div class="search_bar">
      <NoteSearch>
        <el-button slot="append" icon="el-icon-plus" v-on:click="newNbClick"/>
      </NoteSearch>
    </div>
    <el-menu v-loading="loading">
      <el-submenu v-for="nb in notebooks" :key="nb.id" :index="nb.id">
        <template slot="title">
          <i class="el-icon-edit-outline"></i>
          <span>{{nb.name}}</span>
        </template>
        <el-menu-item index="1"><i class="el-icon-document"></i> Note1</el-menu-item>
        <el-menu-item index="2"><i class="el-icon-document"></i> Note2</el-menu-item>
      </el-submenu>
    </el-menu>
    <div class="nodata" v-if="!loading && notebooks.length == 0">
      <i class="el-icon-edit-outline" style="font-size: 60px;"></i><br><br>
      No notebooks,<br>try to create one~
    </div>
  </div>
</template>

<script>
import api from '../utils/api'
import NoteSearch from './NoteSearch'

export default {
  components: { NoteSearch },
  data () {
    return {
      loading: false,
      notebooks: []
    }
  },
  methods: {
    loadList () {
      this.loading = true
      api('notebook/all', {}).then(data => {
        this.notebooks = data
      }).catch(reason => {
        this.$message({
          showClose: true,
          type: 'error',
          message: reason
        })
      }).then(() => {
        this.loading = false
      })
    },
    newNbClick () {
      this.$prompt('Name your new notebook', 'New notebook', {
        confirmButtonText: 'New',
        cancelButtonText: 'Cancel',
        inputPattern: /[^\s]+/,
        inputErrorMessage: 'Please type a notebook name'
      }).then(({ value }) => {
        api('notebook/create', {
          name: value
        }).then(data => {
          this.$message({
            showClose: true,
            type: 'success',
            message: 'Notebook created'
          })
          this.loadList()
        }).catch(reason => {
          this.$message({
            showClose: true,
            type: 'error',
            message: reason
          })
        })
      })
    }
  },
  mounted () {
    this.loadList()
  }
}
</script>

<style lang="less" scoped>
.search_bar {
  padding: 0 20px 10px 20px;
  display: flex;

  .note-search {
    flex: 1;
  }
}

.el-menu {
  min-height: 60px;
}

.el-menu, .el-submenu>:nth-child(2) {
  background: transparent;
}

.el-menu-item, .el-submenu>:first-child {
  height: 30px;
  line-height: 30px;
}

.nodata {
  text-align: center;
}
</style>
