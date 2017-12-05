import axios from 'axios'

const base = 'http://localhost:8080/';

export default function (path, data) {
    return new Promise((resolve, reject) => {
        axios.post(path, data, {
            baseURL: base,
            headers: {
                'X-Auth-Token': localStorage.token
            },
            timeout: 5000,
            responseType: 'json'
        }).then(resp => {
            if (resp.status == 200) {
                if (resp.data.success > 0) {
                    resolve(resp.data.data)
                } else {
                    reject(resp.data.msg)
                }
            } else {
                reject('Unknown error')
            }
        }).catch(reason => reject(reason))
    })
}