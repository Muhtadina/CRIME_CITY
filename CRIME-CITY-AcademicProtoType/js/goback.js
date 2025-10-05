let btnBack = Document.querySelector('button');

btnBack.AddEventListner('click', () => {
    window.history.back();
});