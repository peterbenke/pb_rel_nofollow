.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt


.. _configuration-exclude-urls:

Exclude URLs
------------

You can exclude URLs from this procedure by Typoscript.

Example:

::

    tx_pb_rel_nofollow {
        excludeUrls{
            100 = http://typo3.org/
            110 = http://forge.typo3.org/
        }
    }

Please take for each URL any, but different number (these are the keys of the intern array).
Links, which are beginning with this pattern, will not get the rel-nofollow-attribute.