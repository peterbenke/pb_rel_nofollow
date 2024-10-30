# TYPO3 Extension ``pb_rel_no_follow`` 

## Introduction

Checks, if there are updates available for installed extensions and sends an email


## What does it do?

This extension adds a rel-nofollow-attribute to all extern links. You can exclude URLs by typoscript.


## Administration

### Installation

Install this extension via composer
    
    composer req peterbenke/pb-rel-nofollow

    => Then include the static Typoscript in your template

### Configuration

Exclude URLs

You can exclude URLs from this procedure by Typoscript.

Example:

    tx_pb_rel_nofollow {
        excludeUrls{
            100 = http://typo3.org/
            110 = http://forge.typo3.org/
        }
    }

Please take for each URL any, but different number (these are the keys of the intern array).
Links, which are beginning with this pattern, will not get the rel-nofollow-attribute.

