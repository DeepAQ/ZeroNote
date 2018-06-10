<template>
  <el-dialog :title="type == 1 ? 'Following' : 'Follower'" :visible.sync="show">
    <el-table :data="list" stripe v-loading="loading">
      <el-table-column label="User">
        <template slot-scope="prop">
          <span>{{ prop.row.nickname }} &lt;{{ prop.row.email }}&gt;</span>
        </template>
      </el-table-column>
      <el-table-column label="Actions" width="100" v-if="type == 1">
        <template slot-scope="prop">
          <el-button type="text" @click="unfollow(prop.row.id)">Unfollow</el-button>
        </template>
      </el-table-column>
    </el-table>
    <div class="add" v-if="type == 1">
      <div>
        <el-input v-model="email" placeholder="E-mail ...">
          <span slot="prepend">Follow someone</span>
        </el-input>
      </div>
      <el-button type="primary" icon="el-icon-star-off" v-on:click="doFollow">Follow</el-button>
    </div>
  </el-dialog>
</template>

<script>
import api from '../utils/api'

export default {
  data () {
    return {
      loading: false,
      show: false,
      type: 1,
      list: [],
      email: ''
    }
  },
  methods: {
    open (type) {
      this.show = true
      this.type = type
      this.loadData()
    },
    loadData () {
      this.loading = true
      const apiPath = (this.type == 1) ? 'timeline/followings' : 'timeline/followers'
      api(apiPath, {}).then(data => {
        this.list = data
      }).catch(reason => {
        this.$message({
          showClose: true,
          type: 'error',
          message: reason
        })
      }).then(() => this.loading = false)
    },
    doFollow () {
      if (this.email != '') {
        api('timeline/follow', {
          email: this.email
        }).then(data => {
          this.$message({
            showClose: true,
            type: 'success',
            message: `Followed ${this.email}`
          })
          this.email = ''
          this.loadData()
        }).catch(reason => {
          this.$message({
            showClose: true,
            type: 'error',
            message: `Follow failed: ${reason}`
          })
        })
      } else {
        this.$message({
            showClose: true,
            type: 'error',
            message: 'Please type E-mail'
          })
      }
    },
    unfollow (id) {
      api('timeline/unfollow', {
        userid: id
      }).then(data => {
        this.loadData()
      }).catch(reason => {
        this.$message({
          showClose: true,
          type: 'error',
          message: `Unfollow failed: ${reason}`
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
