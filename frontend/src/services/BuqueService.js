class BuqueService{
    axios
    baseUrl

    constructor(axios,baseUrl){
        this.axios = axios
        this.baseUrl = `${baseUrl}buques`
    }

    getAll(){
        let self = this
        return self.axios.get(`${self.baseUrl}`)
    }
}

export default BuqueService