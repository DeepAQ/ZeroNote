<template>
  <div class="user">
    <el-dropdown v-on:command="onCommand">
      <span class="el-dropdown-link">
        {{nickname}} &lt;{{email}}&gt; <i class="el-icon-arrow-down el-icon--right"></i>
      </span>
      <el-dropdown-menu slot="dropdown">
        <el-dropdown-item command="changenick">Change nickname</el-dropdown-item>
        <el-dropdown-item command="changepass">Change password</el-dropdown-item>
        <el-dropdown-item command="logout">Logout</el-dropdown-item>
      </el-dropdown-menu>
    </el-dropdown>
    <el-dialog title="Change password" width="500px" :visible.sync="showChangePass" class="dialog">
      <el-input v-model="password" type="password" placeholder="Current Password" required/>
      <el-input v-model="newpass" type="password" placeholder="New Password" required/>
      <el-input v-model="newpass2" type="password" placeholder="Re-type password" required/>
      <div slot="footer">
        <el-button @click="showChangePass = false">Cancel</el-button>
        <el-button type="primary" v-on:click="changePassword">OK</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import api from '../utils/api'

export default {
  data () {
    return {
      nickname: localStorage.nickname,
      email: localStorage.email,
      showChangePass: false,
      password: '',
      newpass: '',
      newpass2: ''
    }
  },
  methods: {
    onCommand (cmd) {
      switch (cmd) {
        case 'changenick':
          this.$prompt('Choose a new nickname', 'Change nickname', {
            inputPattern: /[^\s]+/,
            inputErrorMessage: 'Please type a nickname',
            inputValue: this.nickname
          }).then(({ value }) => {
            api('auth/changenickname', {
              nickname: value
            }).then(data => {
              this.$message({
                showClose: true,
                type: 'success',
                message: 'Nickname changed'
              })
              this.nickname = value
              localStorage.nickname = value
            }).catch(reason => {
              this.$message({
                showClose: true,
                type: 'error',
                message: reason
              })
            })
          })
          break
        case 'changepass':
          this.showChangePass = true
          break
        case 'logout':
          delete localStorage['token']
          window.location = '/auth.html'
          break
      }
    },
    changePassword () {
      if (this.password == '' || this.newpass == '' || this.newpass2 == '') {
        this.$message({
          showClose: true,
          type: 'error',
          message: 'Please type old and new passwords'
        })
        return
      }
      if (this.newpass != this.newpass2) {
        this.$message({
          showClose: true,
          type: 'error',
          message: 'New passwords mismatch'
        })
        return
      }
      api('auth/changepassword', {
        password: this.password,
        newpassword: this.newpass
      }).then(data => {
        this.$message({
          showClose: true,
          type: 'success',
          message: 'Password changed'
        })
        this.showChangePass = false
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
.dialog {
  .el-input {
    margin-top: 10px;
  }
}
</style>
