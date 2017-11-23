import _ from 'lodash'

export default {

    stencils: [
        {
            "type": "websitelogin",
            "title": "Website Login",
            "icon": "globe", // glyphicon
            "vault": {
                "title": "Vaults",
                "size": 10,
                "filterSelf": false, // whether to filter itself out of the vault list
                "createNew": true, // whether a new vault can be created
            },
            "fields": {
                "url": {
                    "title": "URL",
                    "component": "PlainText",
                    "size": 6
                },
                "domain": {
                    "title": "Domain",
                    "component": "PlainText",
                    "size": 6,
                    "tooltip": "Easily categorize and autofill passwords with matching domain names"
                },
                "username": {
                    "title": "Username",
                    "component": "PlainText",
                    "size": 6
                },
                "password": {
                    "title": "Password",
                    "component": "Password",
                    "encrypted": true,
                    "copyable": true,
                    "required": true,
                    "size": 6
                },
                "notes": {
                    "title": "Notes",
                    "component": "PlainText",
                    "encrypted": true,
                    "height": 3
                }
            }
        },
        {
            "type": "vault",
            "title": "Vault",
            "icon": "book",
            "vault": {
                "title": "Parents",
                "size": 10,
                "filterSelf": true,
                "createNew": false
            },
            "fields": {
                "notes": {
                    "title": "Notes",
                    "component": "PlainText",
                    "height": 3
                }
            }
        },
        {
            "type": "note",
            "title": "Note",
            "icon": "file",
            "vault": {
                "title": "Vaults",
                "size": 10,
                "filterSelf": false,
                "createNew": true
            },
            "fields": {
                "content": {
                    "title": "Note",
                    "component": "PlainText",
                    "encrypted": true,
                    "height": 3
                }
            }
        }
    ],

    allTypes () {
        return _(this.stencils).map((v) => {
            return { title: v.title, type: v.type, icon: v.icon }
        }).value()
    },

    get (type) {
        return _(this.stencils).find(v => v.type === type)
    }

}