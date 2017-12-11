<template>
  <el-dialog title="Note sharing" :visible.sync="show">
    <div v-loading="loading">
      <div style="line-height: 30px;">
        <el-switch v-model="isPublic" v-on:change="setPublic"/>
        <span style="margin-left: 5px;">
          Public sharing (all followers have read permission)
        </span>
      </div>
      <el-table :data="shared">
        <el-table-column
          label="User">
          <template slot-scope="prop">
            <span v-if="prop.row.touser">{{ prop.row.touser.nickname }} &lt;{{ prop.row.touser.email }}&gt;</span>
            <span v-else>Unknown user ({{ prop.row.touserid }})</span>
          </template>
        </el-table-column>
        <el-table-column
          label="Permission"
          width="150">
          <template slot-scope="prop">
            <span v-if="prop.row.permission == 1">Write</span>
            <span v-else>Read</span>
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
        <el-switch v-model="editable" inactive-text="Read-only" active-text="Editable"/>
        <el-button type="primary" icon="el-icon-share" v-on:click="doShare">Share</el-button>
      </div>
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
      isPublic: false,
      email: '',
      editable: false,
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
        this.isPublic = (data.public == 1)
        this.shared = data.shared
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
        email: this.email,
        editable: this.editable
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
    },
    setPublic () {
      api('share/setpublic', {
        noteid: this.id,
        public: this.isPublic
      }).then(data => {
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
  align-items: center;
  margin-top: 10px;

  >:first-child {
    flex: 1;
  }

  >:nth-child(2) {
    margin: 0 10px;
  }
}
</style>

<style lang="less">
.el-dialog__body {
  padding-top: 0;
}
</style>
