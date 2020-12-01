## Clavatar - A Clone Of Gravatar

Clavatar is for learning purposes. The Image service does the following:

- Return a default image of sixe (100 x 100) if the hash is not valid or if the user does not have any avatar attached to that email address.
- Allow you to request for any size of the image by attaching a size parameter like so **s=400** or **size=400** to the URL.
- Allow you to return a cartoonized version of an image by attaching **d=cp** to the URL
- Allow you to request for the black and white version of the image by attaching a **d=bw** to the URL.
- Allow you to request for an artistic version of the image by attaching a **d=hk** to the URL.
- Allow you to request for the compressed and optimized version of the image by attaching a **q=auto** to the URL.
- Allow you to request for the best format of the image  by attaching a **f=auto** to the URL. This will ensure that whatever browser is delivering the image gets the best format.
- Allow you to request for the rounded corners version of the image (just as we have in every platform’s thumbnail profile image) by attaching a **rc=y** to the URL.
- Allow you to rotate the image by attaching a **r=40** to the URL. 40 refers to the angle. It can be any value between 1 and 100.
- Allow you to request for a color-bordered version of the image by attaching a **b=red** to the URL. red refers to the color. Any color can be specified here.
