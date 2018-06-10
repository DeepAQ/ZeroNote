<template>
  <el-dialog title="Trending" :visible.sync="show">
    <el-table :data="list" stripe
      row-class-name="pointer"
      v-loading="loading"
      v-on:row-click="rowClick">
      <el-table-column label="Title">
        <template slot-scope="prop">
          <span>{{ prop.row.title == '' ? 'Untitled' : prop.row.title }}</span>
        </template>
      </el-table-column>
      <el-table-column label="Upvotes" width="100">
        <template slot-scope="prop">
          <i class="el-icon-star-on"></i>
          {{ prop.row.upvotes }}
        </template>
      </el-table-column>
    </el-table>
  </el-dialog>
</template>

<script>
import api from '../utils/api'

export default {
  data () {
    return {
      loading: false,
      show: false,
      list: []
    }
  },
  methods: {
    open () {
      this.show = true
      this.loadData()
    },
    loadData () {
      this.loading = true
      api('timeline/leaderboard', {}).then(data => {
        this.list = data
      }).catch(reason => {
        this.$message({
          showClose: true,
          type: 'error',
          message: reason
        })
      }).then(() => this.loading = false)
    },
    rowClick (row, event, column) {
      this.$router.push(`/timeline/${row.id}`)
      this.show = false
    }
  }
}
</script>

<style lang="less">
.el-dialog__body {
  padding-top: 0;
}

.pointer {
  cursor: pointer;
}
</style>
