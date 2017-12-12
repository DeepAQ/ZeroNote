<template>
  <div>
    <div class="head">
      <el-button-group>
        <el-button type="primary" @click="openFollow(1)">
          <div class="btnicon"><i class="el-icon-star-off"></i></div>
          Following
        </el-button>
        <el-button type="primary" @click="openFollow(2)">
          <div class="btnicon"><i class="el-icon-view"></i></div>
          Follower
        </el-button>
        <el-button type="primary" v-on:click="openLeaderboard">
          <div class="btnicon"><i class="el-icon-sort"></i></div>
          Leaderboard
        </el-button>
      </el-button-group>
    </div>
    <div class="timeline" v-loading="loading">
      <div class="item" v-for="item in timeline" :key="item.id" v-on:click="itemClick(item.id)">
        <div class="circle"></div>
        <div v-if="item.user">{{ item.user.nickname }} &lt;{{ item.user.email }}&gt;</div>
        <div>Updated: {{ item.title }}</div>
        <div class="time">{{ (new Date(item.updated * 1000)).toLocaleString() }}</div>
      </div>
      <div class="nodata" v-if="!loading && timeline.length == 0">
        <i class="el-icon-time" style="font-size: 60px;"></i><br>
        No activities,<br>try to follow someone~
      </div>
    </div>
    <FollowList ref="follow"/>
    <Leaderboard ref="leaderboard"/>
  </div>
</template>

<script>
import FollowList from '../components/FollowList'
import Leaderboard from '../components/Leaderboard'
import api from '../utils/api'

export default {
  components: { FollowList, Leaderboard },
  data () {
    return {
      loading: false,
      timeline: []
    }
  },
  mounted () {
    this.loadTimeline()
  },
  methods: {
    loadTimeline () {
      this.loading = true
      api('timeline/fetch', {}).then(data => {
        this.timeline = data
      }).catch(reason => {
        this.$message({
          showClose: true,
          type: 'error',
          message: reason
        })
      }).then(() => this.loading = false)
    },
    itemClick (id) {
      this.$router.push(`/timeline/${id}`)
    },
    openFollow (type) {
      this.$refs.follow.open(type)
    },
    openLeaderboard () {
      this.$refs.leaderboard.open()
    }
  }
}
</script>

<style lang="less" scoped>
.head {
  text-align: center;

  .btnicon {
    font-size: 24px;
    line-height: 32px;
  }

  .el-button {
    padding: 10px 12px;
  }
}

.timeline {
  border-left: 3px solid #ddd;
  margin: 20px 0 0 20px;
  min-height: 50px;

  .nodata {
    text-align: center;
  }

  .item {
    padding: 10px;
    font-size: 14px;

    .circle {
      width: 10px;
      height: 10px;
      background-color: whitesmoke;
      position: absolute;
      margin-left: -18px;
      margin-top: 4px;
      border: #6e0f6d solid 1.5px;
      border-radius: 50%;
    }

    .time {
      font-size: 12px;
      color: #aaa;
    }
  }

  .item:hover {
    cursor: pointer;
    background-color: rgba(110, 15, 109, 0.1);
  }
}
</style>
