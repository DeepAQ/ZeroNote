<template>
  <div>
    <div class="search_bar">
      <NoteSearch/>
    </div>
    <el-menu>
      <el-submenu v-for="nb in notebooks" :key="nb.id" :index="nb.id">
        <template slot="title">
          <i class="el-icon-edit-outline"></i>
          <span>{{nb.name}}</span>
        </template>
        <el-menu-item index="1"><i class="el-icon-document"></i> Note1</el-menu-item>
        <el-menu-item index="2"><i class="el-icon-document"></i> Note2</el-menu-item>
      </el-submenu>
    </el-menu>
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
        console.log(reason)
      }).then(() => {
        this.loading = false
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

.el-menu-item, .el-submenu>:first-child {
  height: 30px;
  line-height: 30px;
}
</style>
