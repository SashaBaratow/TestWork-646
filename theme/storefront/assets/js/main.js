let clearFieldsBtn = document.querySelector('.button-clear')
let inputDate = document.querySelector('.input-date')
let removeMediaBtn = document.querySelector('.remove-media')
let customUpdateBtn = document.querySelector('.button-save')
let updateBtn = document.querySelector('#publishing-action input#publish')
clearFieldsBtn.addEventListener('click', (e)=>{
    e.preventDefault()
    setDefoultDate()
    deleteImg()
})
customUpdateBtn.addEventListener('click', (e)=>{
    e.preventDefault()
    updateBtn.click()
})
function setDefoultDate(){
    Date.prototype.toDateInputValue = (function() {
        var local = new Date(this);
        local.setMinutes(this.getMinutes() - this.getTimezoneOffset());
        return local.toJSON().slice(0,10);
    });
    // inputDate.value = '00 00 0000'
    inputDate.value = new Date().toDateInputValue();
}
function deleteImg(){
    removeMediaBtn.click()
}
//select don't have default value