// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';
import 'tw-elements';
// Initialization for ES Users
import {
    Collapse,
    Dropdown,
    Input,
    Modal,
    Ripple,
    initTWE,
} from "tw-elements";
import Like from './scripts/like';

// Tw-elements
initTWE({ Collapse, Dropdown, Input, Modal, Ripple }, { allowReinits: true });

console.log('Webpack Encore is working ! ');

// Likes register
document.addEventListener('DOMContentLoaded', () => {
    // Like's system
    const likeElements = [].slice.call(document.querySelectorAll('a[data-action="like"]'));
    if(likeElements) {
        new Like(likeElements);
    }
})

// Quick Modal fix ->  refresh page
const refreshButton = document.querySelector('#refreshButton');
if (refreshButton) {
    refreshButton.addEventListener('click', function() {
        location.reload();
    });
}

