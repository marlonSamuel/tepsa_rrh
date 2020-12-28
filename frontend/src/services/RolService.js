class RolService{
    axios
    baseUrl

    constructor(axios,baseUrl){
        this.axios = axios
        this.baseUrl = `${baseUrl}roles`
    }

    getAll(){
        let self = this
        return self.axios.get(`${self.baseUrl}`)
    }
}

export default RolService