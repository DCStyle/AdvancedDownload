# Advanced Download
Create a new download page for your attachments & resources, makes your website much more professional. By using this add-on, there are some advantages:

- More profit by adding ads to download page.
- Prevent download from accidental clicks which may slow down the server.

# Installation
1. Download and unzip it
2. Upload to your server
3. Install the add-on from AdminCP
4. To config the add-on, go Options then Advanced Download

# Extra Information
For those who have problems with **Google Adsense**. It seems that the default XenForo display system conflicts with Google Ads. 
To fix this you should edit the two templates `DC_AdvancedDownload_DownloadInternal` and `DC_AdvancedDownload_DownloadExternal`. 
In each one, replace this code with your left ads code

`{$xf.options.DC_AdvancedDownload_adLeft|raw}`

And this code with your right ads code

`{$xf.options.DC_AdvancedDownload_adRight|raw}`

# Note
This add-on support attachment, internal and external resources.