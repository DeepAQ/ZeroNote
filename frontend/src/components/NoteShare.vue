<template>
  <el-dialog title="Note sharing" :visible.sync="show">
    <el-table :data="shared" v-loading="loading">
      <el-table-column
        label="User">
        <template slot-scope="prop">
          <span v-if="prop.row.touser">{{ prop.row.touser.nickname }} &lt;{{ prop.row.touser.email }}&gt;</span>
          <span v-else>Unknown user ({{ prop.row.touserid }})</span>
        </template>
      </el-table-column>
      <el-table-column
        label="Actions"
        width="100">
        <template slot-scope="prop">
          <el-button type="text" @click="revokeShare(prop.row.id)">Revoke</el-button>
        </template>
      </el-table-column>
    </el-table>
    <div class="add">
      <div>
        <el-input v-model="email" placeholder="E-mail ...">
          <span slot="prepend">Share to</span>
        </el-input>
      </div>
      <el-button type="primary" v-on:click="doShare">Share</el-button>
    </div>
  </el-dialog>
</template>

<script>
import api from '../utils/api'

export default {
  data () {
    return {
      loading: false,
      id: 0,
      show: false,
      email: '',
      shared: []
    }
  },
  methods: {
    showShare (id) {
      this.id = id
      this.show = true
      this.loadShare()
    },
    loadShare () {
      this.loading = true
      api('share/single', {
        noteid: this.id
      }).then(data => {
        this.shared = data
      }).catch(reason => {
        this.$message({
          showClose: true,
          type: 'error',
          message: reason
        })
      }).then(() => this.loading = false)
    },
    doShare () {
      if (this.email == '') {
        return
      }
      api('share/add', {
        noteid: this.id,
        email: this.email
      }).then(data => {
        this.$message({
          showClose: true,
          type: 'success',
          message: `Shared to ${this.email}`
        })
        this.email = ''
        this.loadShare()
      }).catch(reason => {
        this.$message({
          showClose: true,
          type: 'error',
          message: reason
        })
      })
    },
    revokeShare (id) {
      api('share/revoke', {
        id: id
      }).then(data => {
        this.$message({
          showClose: true,
          type: 'success',
          message: 'Successfully revoked'
        })
        this.loadShare()
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

<style lang="less" scoped>
.add {
  width: 100%;
  display: flex;
  margin-top: 10px;

  >:first-child {
    flex: 1;
  }
}
</style>

<style lang="less">
.el-dialog__body {
  padding-top: 0;
}
</style>
