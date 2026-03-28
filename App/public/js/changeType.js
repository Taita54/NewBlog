/* 
 * This software license is property of GDMSOFTANDPICT.
 * you and everyone other at your place, can't
 * use all or parts of this software,  whithout permission of
 * GDMSOFTANDPICT.
 */
function changeType() {
    let con;

    const doc = document;
    const sel = doc.getElementById('type').value; //il tipo di pubblicazione selezionata
    const input = doc.querySelector('#image_uploads');
    const labInpt=doc.querySelector('.lab_image_uploads');
    const preview = doc.querySelector('.preview');
    const tit = doc.querySelector('#title');//il valore del titolo
    const siz = doc.querySelector('#size');//il valore dell'attributo size
    const siz_disp=doc.querySelector('#size_display');//il campo ove viene visualizzata la dimensione in kb
    const dtc = doc.querySelector('#date_created');//il valore dell'attributo data di creazione
    
    con = sel.trim();

    switch (con){
        case 'imm':
            //input.style.opacity = 0;
            input.addEventListener('change', updateImageDisplay);
            labInpt.style.opacity=100;
            doc.querySelector('.img_viewer').style.display="block";
            doc.querySelector('#image_uploads').style.display="block";
            doc.querySelector('.lab_art_uploads').style.display="none";
            doc.querySelector('#cke_editor1').style.display="none";
        break;
    case 'html':
            labInpt.style.opacity=0;
            doc.querySelector('.img_viewer').style.display="none";
            doc.querySelector('#image_uploads').style.display="none";
            doc.querySelector('.lab_art_uploads').style.display="block";
            doc.querySelector('#cke_editor1').style.display="block";
    }

function updateImageDisplay() {
 // while(preview.firstChild) {
    preview.removeChild(preview.firstChild);
 // }

  const curFiles = input.files;
  if(curFiles.length === 0) {
    const para = doc.createElement('p');
    para.textContent = 'No files currently selected for upload';
    preview.appendChild(para);
  } else {
    const list = doc.createElement('ol');
    //list.classList.add('img_container');
    preview.appendChild(list);

   for(const file of curFiles) {
        const listItem = doc.createElement('li');
        const para = doc.createElement('p');
        if(validFileType(file)) {
            tit.value = file.name;
            siz.value=file.size;
            siz_disp.value = returnFileSize(file.size);
            dtc.value = convertDate(file.lastModified);

            para.textContent = `File name ${file.name}.`;
            const image = doc.createElement('img');
            image.src = URL.createObjectURL(file);
            image.height = '300';
            image.preserveAspectRatio = 'xMidYMid slice';
            image.classList.add('rounded');
            image.classList.add('mx-auto');
            image.classList.add('d-block');
            image.classList.add('img-responsive');
            list.appendChild(image);
            list.appendChild(para);
        } else {
            para.textContent = `File name ${file.name}: Not a valid file type. Update your selection.`;
            list.appendChild(para);
      }

      //list.appendChild(listItem);
    }
  }
}

    // https://developer.mozilla.org/en-US/docs/Web/Media/Formats/Image_types
    const fileTypes = [
      "image/apng",
      "image/bmp",
      "image/gif",
      "image/jpeg",
      "image/jpg",
      "image/png",
      "image/svg+xml",
      "image/tiff",
      "image/webp",
      "image/x-icon"
    ];

    function validFileType(file) {
        return fileTypes.includes(file.type);
    }

    function returnFileSize(number) {
        if(number < 1024) {
          return number + 'bytes';
        } else if(number >= 1024 && number < 1048576) {
          return (number/1024).toFixed(1) + 'KB';
        } else if(number >= 1048576) {
          return (number/1048576).toFixed(1) + 'MB';
        }
    }
    
    function convertDate(dt){
        //questo è il  modo più chiaro che ho trovato
        //per formattare una data con javascript
       if(dt){
       }else{
           dd=new Date();
           dt=dd.getTime();
       }
        const d = new Date(dt); // new Date(file.lastModified);
        const ye = new Intl.DateTimeFormat('en', { year: 'numeric' }).format(d);
        const mo = new Intl.DateTimeFormat('en', { month: '2-digit' }).format(d);
        const da = new Intl.DateTimeFormat('en', { day: '2-digit' }).format(d);
        let result = `${ye}-${mo}-${da}`;
        return result;
    }
}