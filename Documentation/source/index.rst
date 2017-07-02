Welcome to Codappix TYPO3 Core Extension's documentation!
=========================================================

This extension provides code to ease custom migrations of data inside of Databases.

The extension provides ActiveRecord pattern to ease migrations. Also it provides some defaults to
add static values through TypoScript configuration.

It also establishes database connections using the TYPO3 connection configuration.

In addition, defaults using TCA are provided, e.g. ``cruser_id`` or ``crdate``.

Configuration
-------------

.. code-block:: text

   module.tx_cdxmigration {
       dbConnection {
           old {
                username =
                password =
                host =
                db =
           }
       }

       model {
           <modelName> {
               values {
                   columns = value
               }
           }
       }
   }

Where ``<modleName>`` is the last part of FQCN excluding ``New`` prefix. E.g. the following class:
``Codappix\CdxSite\Migration\Model\NewRecordType`` is configured with ``RecordType``.

Each model mapping to old DB should extend ``Codappix\CdxMigration\Migration\Model\From``.
Each model mapping to new DB should extend ``Codappix\CdxMigration\Migration\Model\To``.

This makes sure the right connection is used, without further configuration. Also the default
mappings are applied.
