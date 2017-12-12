<template>
  <div id="admin">
    <header>ZeroNote Administration</header>
    <el-table :data="list">
      <el-table-column label="E-mail">
        <template slot-scope="prop">
          {{ prop.row.email }}
        </template>
      </el-table-column>
      <el-table-column label="Nickname">
        <template slot-scope="prop">
          {{ prop.row.nickname }}
        </template>
      </el-table-column>
      <el-table-column label="Register time">
        <template slot-scope="prop">
          {{ new Date(prop.row.created * 1000).toLocaleString() }}
        </template>
      </el-table-column>
      <el-table-column label="Action" width="150">
        <template slot-scope="prop">
          <el-button type="text" @click="setDisabled(prop.row.id, 1)" v-if="prop.row.disabled == 0">Disable</el-button>
          <el-button type="text" @click="setDisabled(prop.row.id, 0)" v-else>Enable</el-button>
        </template>
      </el-table-column>
    </el-table>
  </div>
</template>

<script>
import api from '../utils/api'

export default {
  data () {
    return {
      list: []
    }
  },
  mounted () {
    this.loadData()
  },
  methods: {
    loadData () {
      api('admin/getall', {}).then(data => {
        this.list = data
      }).catch(reason => {
        this.$message({
          showClose: true,
          type: 'error',
          message: reason
        })
      })
    },
    setDisabled (id, disabled) {
      api('admin/setdisabled', {
        id: id,
        disabled: disabled
      }).then(data => {
        this.loadData()
      }).catch(reason => {
        this.$message({
          showClose: true,
          type: 'error',
          message: reason
        })
      })
    }
  }
}
</script>

<style lang="less">
#admin {
  header {
    background-color: #6e0f6d;
    color: whitesmoke;
    font-size: 28px;
    font-weight: lighter;
    padding: 5px 20px;
  }

  .el-table {
    width: 1000px;
    margin: 10px auto;
  }
}
</style>
