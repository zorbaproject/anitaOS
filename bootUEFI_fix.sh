mnt="/boot/efi"
#efibootmgr -v
OLD=$(date +%d%m%y$H%M)

mv $mnt/EFI/Boot/bootx64.efi $mnt/EFI/Boot/bootx64${OLD}.efi
#mv $mnt/EFI/Boot/bkpbootx64.efi $mnt/EFI/Boot/bkpbootx64${OLD}.efi
mv $mnt/EFI/Microsoft/Boot/bootmgfw.efi $mnt/EFI/Microsoft/Boot/bootmgfw${OLD}.efi

#When using Secure Boot: $mnt/EFI/ubuntu/shimx64.efi
#When not using Secure Boot: $mnt/EFI/ubuntu/grubx64.efi
cp $mnt/EFI/ubuntu/shimx64.efi $mnt/EFI/Boot/bootx64.efi
#cp $mnt/EFI/ubuntu/grubx64.efi $mnt/EFI/Boot/bootx64.efi
cp $mnt/EFI/ubuntu/shimx64.efi $mnt/EFI/Microsoft/Boot/bootmgfw.efi
cp $mnt/EFI/ubuntu/grubx64.efi $mnt/EFI/Microsoft/Boot/grubx64.efi

#questo non funziona ma copia e incolla si:
cat << EOF > /etc/grub.d/40_custom
#!/bin/sh
exec tail -n +3 $0
# This file provides an easy way to add custom menu entries.  Simply type the
# menu entries you want to add after this comment.  Be careful not to change
# the 'exec tail' line above.
menuentry 'Windows Boot Manager OK (su /dev/sda2)' --class windows {
        insmod part_gpt
        insmod fat
        set root='hd0,gpt2'
        if [ x$feature_platform_search_hint = xy ]; then
          search --no-floppy --fs-uuid --set=root --hint-bios=hd0,gpt2 --hint-efi=hd0,gpt2 --hint-baremetal=ahci0,gpt2  8AC9-C7A7
        else
          search --no-floppy --fs-uuid --set=root 8AC9-C7A7
        fi
        chainloader /EFI/Microsoft/Boot/bootmgfw${OLD}.efi
}
EOF

update-grub

#umount /mnt

