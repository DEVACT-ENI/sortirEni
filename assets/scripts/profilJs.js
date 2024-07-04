document.addEventListener('DOMContentLoaded', () => {
    console.log("Profil JS loaded");
    let form = document.getElementsByName('photo_profil')[0];

    form.addEventListener('change', function() {
        document.getElementsByName('photo_profil')[0].submit();
        console.log("submit");
    });
});